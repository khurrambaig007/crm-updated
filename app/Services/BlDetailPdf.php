<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Agent;
use App\Models\BookingBlDetail;
use App\Models\CompanyProfile;
use App\Models\Party;
use App\Models\Pod;
use App\Models\Pol;
use App\Models\ShipperBp;
use App\Models\VesselVoyage;
use RuntimeException;
use setasign\Fpdi\Fpdi;

/**
 * Renders a BL Detail record onto the pre-printed Combined Transport Bill of
 * Lading artwork.
 *
 * The template (resources/pdf/bl-detail-template.pdf) is Illustrator vector
 * artwork with every glyph converted to an outline, so it carries no text layer
 * and no AcroForm fields. There is nothing to populate or extract from it — it
 * is imported as-is and values are stamped on top at absolute coordinates.
 *
 * Page 2 is the terms and conditions reverse side and is never written to.
 *
 * The artwork has a logo baked into it, which would be a hardcoded logo that
 * no longer matches the company using the application. It is covered and
 * replaced by the Company Profile logo on every render.
 *
 * All coordinates are PDF points measured from the top-left of an A4 portrait
 * page (595.3 x 841.9pt). They were read off the template's own ruled lines, so
 * the text lands inside its boxes; re-derive them if the artwork is replaced.
 */
class BlDetailPdf extends Fpdi
{
    /**
     * Text zones on page 1.
     *
     * y/width/height describe the box the value must stay inside. Widths are
     * used for wrapping, heights to cap the number of lines so long free-text
     * particulars cannot spill past the ruled border and onto the row beneath.
     *
     * @var array<string, array{float, float, float, float, float}>
     */
    private const ZONES = [
        // Bill of Lading No. box (x 413.7..555.6, y 39.9..59.3). The caption
        // is printed inside the box at its top, so the value starts below it.
        'bl_number' => [417.0, 48.0, 135.0, 11.0, 9.0],

        // Shipper / consignee row (y 117.1..188.1), notify row (188.1..257.7).
        // These captions are printed inside the box, so the value starts well
        // below them — ~6.5pt of clear space, the same gap the delivery-agent
        // box uses. Height stops short of the row rule.
        'shipper' => [46.0, 129.5, 242.0, 54.0, 8.0],
        'consignee' => [296.0, 129.5, 251.0, 54.0, 8.0],
        'notify' => [46.0, 201.5, 242.0, 52.0, 8.0],

        // Routing strip. Each cell is ~26pt tall with the printed caption
        // occupying the top ~10pt, hence the single-line heights.
        'place_of_receipt' => [46.0, 270.5, 114.0, 12.0, 8.0],
        'pol' => [168.0, 270.5, 120.0, 12.0, 8.0],
        'vessel' => [46.0, 297.0, 242.0, 13.0, 8.0],
        'pod' => [46.0, 323.3, 114.0, 12.0, 8.0],
        'place_of_delivery' => [168.0, 323.3, 120.0, 12.0, 8.0],

        // Particulars. The left three columns stop at y 608.4 where the excess
        // value box begins; the two right columns run down to y 635.6. The
        // column captions live in the header row above the rule at y 375.6,
        // so the values clear that rule rather than the caption itself.
        'marks' => [45.0, 380.0, 90.0, 226.0, 7.0],
        'packages' => [141.0, 380.0, 58.0, 226.0, 7.0],
        'description' => [205.0, 380.0, 208.0, 226.0, 7.0],
        'gross_weight' => [420.0, 380.0, 68.0, 253.0, 7.0],
        'measurement' => [494.0, 380.0, 58.0, 253.0, 7.0],

        // "For delivery of goods please apply to:" sits in the open area right
        // of the freight boxes (y 635.6..709.9).
        'delivery_agent' => [300.0, 648.0, 250.0, 55.0, 8.0],

        // Bottom strip. Place and date of issue use the blank right-hand area
        // of the last row rather than the caption-only cell above it.
        'issue_place' => [300.0, 737.0, 120.0, 12.0, 8.0],
        'issue_date' => [300.0, 752.0, 120.0, 12.0, 8.0],
        'original_bls' => [46.0, 750.0, 110.0, 14.0, 8.0],
    ];

    /**
     * The logo box on page 1: x, y, width, height in PDF points.
     *
     * Measured from the artwork's own embedded logo (the only image on page 1,
     * a 1800x2170 raster placed at Rect(44.53, 42.55, 101.62, 111.28)). The
     * cover rect must stay this tight: the outlined header glyphs sit about 4pt
     * above it and would otherwise be erased.
     *
     * @var array{float, float, float, float}
     */
    private const LOGO_BOX = [44.53, 42.55, 57.09, 68.73];

    private int $pageCount = 0;

    /**
     * Re-encoded logo copies awaiting cleanup after Output().
     *
     * @var array<int, string>
     */
    private array $temporaryLogos = [];

    public function __construct(private readonly string $templatePath)
    {
        parent::__construct('P', 'pt', 'A4');
    }

    /**
     * Build the finished PDF and return it as raw bytes.
     */
    public function render(BookingBlDetail $detail): string
    {
        $this->prepare();

        $this->AddPage();
        $this->useTemplate($this->importPage(1));

        $this->applyLogo();
        $this->stamp($detail);

        // Page 2 is the terms and conditions reverse side — imported untouched.
        if ($this->pageCount > 1) {
            $this->AddPage();
            $this->useTemplate($this->importPage(2));
        }

        try {
            return $this->Output('S', 'bl-detail.pdf');
        } finally {
            $this->discardTemporaryLogos();
        }
    }

    /**
     * Cover the artwork's baked-in logo and stamp the Company Profile logo
     * into the same box, scaled to fit and centred.
     *
     * The cover is unconditional: a stale logo in the artwork must never
     * survive. With no usable logo on the profile the box is simply left blank
     * rather than falling back to the hardcoded one.
     */
    private function applyLogo(): void
    {
        [$x, $y, $width, $height] = self::LOGO_BOX;

        $this->SetFillColor(255, 255, 255);
        $this->Rect($x, $y, $width, $height, 'F');

        $profile = CompanyProfile::current();

        // logoDisplaySize fits the box while preserving aspect ratio; sizing it
        // here rather than via a max-* rule is what keeps a tall logo from
        // collapsing to a sliver.
        $size = $profile->logoDisplaySize((int) round($width), (int) round($height));
        $path = $size === null ? null : $this->embeddableLogo($profile->logoPath());

        if ($size === null || $path === null) {
            return;
        }

        $this->Image(
            $path,
            $x + (($width - $size['width']) / 2),
            $y + (($height - $size['height']) / 2),
            $size['width'],
            $size['height'],
        );
    }

    /**
     * Resolve a logo path FPDF can actually embed.
     *
     * FPDF reads JPEG and PNG only. The profile accepts webp as well, which
     * would raise "Unsupported image type", so it is re-encoded through GD.
     */
    private function embeddableLogo(?string $path): ?string
    {
        if ($path === null || ! is_file($path)) {
            return null;
        }

        $type = @getimagesize($path)[2] ?? null;

        if ($type === IMAGETYPE_JPEG || $type === IMAGETYPE_PNG) {
            return $path;
        }

        $contents = @file_get_contents($path);
        $image = $contents === false ? false : @imagecreatefromstring($contents);

        if ($image === false) {
            return null;
        }

        $scratch = tempnam(sys_get_temp_dir(), 'bl-logo-');

        if ($scratch === false) {
            imagedestroy($image);

            return null;
        }

        $png = $scratch.'.png';
        rename($scratch, $png);

        imagepng($image, $png);
        imagedestroy($image);

        $this->temporaryLogos[] = $png;

        return $png;
    }

    private function discardTemporaryLogos(): void
    {
        foreach ($this->temporaryLogos as $path) {
            @unlink($path);
        }

        $this->temporaryLogos = [];
    }

    private function prepare(): void
    {
        if (! is_readable($this->templatePath)) {
            throw new RuntimeException("BL Detail template not readable at {$this->templatePath}");
        }

        // Zero margins also zero the cell margin, so text lands flush at the
        // exact coordinates instead of being inset.
        $this->SetMargins(0, 0, 0);
        // The artwork decides pagination; never let the writer invent a page.
        $this->SetAutoPageBreak(false, 0);
        $this->SetTextColor(0, 0, 0);

        $this->pageCount = $this->setSourceFile($this->templatePath);

        if ($this->pageCount < 1) {
            throw new RuntimeException('BL Detail template has no pages.');
        }
    }

    private function stamp(BookingBlDetail $detail): void
    {
        foreach ($this->values($detail) as $key => $value) {
            if ($value === null || trim($value) === '') {
                continue;
            }

            [$x, $y, $width, $height, $size] = self::ZONES[$key];

            $this->SetFont('helvetica', '', $size);
            $this->writeBox($this->sanitize($value), $x, $y, $width, $height);
        }
    }

    /**
     * Wrap text to the box width and draw it line by line, dropping anything
     * that would run past the bottom of the box.
     *
     * Wrapping is done here rather than via MultiCell because the line count has
     * to be known up front in order to clamp it — an overflowing particulars
     * description would otherwise be printed across the row below.
     */
    private function writeBox(string $text, float $x, float $y, float $width, float $height): void
    {
        $lineHeight = $this->FontSize * 1.25;
        $maxLines = max(1, (int) floor($height / $lineHeight));

        $lines = $this->wrap($text, $width);

        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $lines[max($maxLines - 1, 0)] = rtrim(substr($lines[max($maxLines - 1, 0)], 0, -3)).'...';
        }

        foreach ($lines as $index => $line) {
            $this->SetXY($x, $y + ($index * $lineHeight));
            $this->Cell($width, $lineHeight, $line, 0, 0, 'L');
        }
    }

    /**
     * @return array<int, string>
     */
    private function wrap(string $text, float $width): array
    {
        $lines = [];

        foreach (preg_split('/\R/', $text) ?: [''] as $paragraph) {
            $words = preg_split('/\s+/', trim($paragraph)) ?: [];
            $current = '';

            foreach ($words as $word) {
                $candidate = $current === '' ? $word : $current.' '.$word;

                if ($this->GetStringWidth($candidate) <= $width || $current === '') {
                    $current = $candidate;
                } else {
                    $lines[] = $current;
                    $current = $word;
                }
            }

            $lines[] = $current;
        }

        return array_values(array_filter($lines, static fn (string $line): bool => $line !== ''));
    }

    /**
     * FPDF's core fonts are Windows-1252. Convert what can be represented and
     * drop the rest, so a stray character cannot render as mojibake on a
     * printed legal document.
     */
    private function sanitize(string $text): string
    {
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $text);

        if ($converted === false) {
            $converted = preg_replace('/[^\x20-\x7E]/', '', $text) ?? $text;
        }

        return trim($converted);
    }

    /**
     * Resolve stored ids and config keys into the labels a bill of lading prints.
     *
     * @return array<string, string|null>
     */
    private function values(BookingBlDetail $detail): array
    {
        $vessel = VesselVoyage::find($detail->bl_detail_vessel_voyage);
        $pol = Pol::find($detail->bl_detail_pol);

        return [
            'bl_number' => $detail->bl_info_bl_number,
            'shipper' => ShipperBp::find($detail->bl_detail_shipper)?->name,
            'consignee' => Party::find($detail->bl_detail_consignee)?->name,
            'notify' => $this->joinNonEmpty([
                $detail->bl_detail_notify_party_1,
                $detail->bl_detail_notify_party_2,
            ]),
            // The artwork has no place-of-receipt box of its own. On port to
            // port movements the goods are received at the loading port, so the
            // POL is carried across rather than leaving a conspicuous hole.
            'place_of_receipt' => $pol?->city,
            'pol' => $pol?->city,
            'vessel' => $vessel === null ? null : trim($vessel->vessel_name.' '.$vessel->voyage_number),
            'pod' => Pod::find($detail->bl_detail_pofd)?->city,
            'place_of_delivery' => $detail->bl_detail_place_of_delivery,
            'marks' => $detail->bl_detail_marks_and_numbers,
            'packages' => $detail->bl_detail_no_of_packages,
            'description' => $detail->bl_detail_description_of_goods,
            // The form has no unit column, so it rides with the figure the way a
            // bill of lading prints it.
            'gross_weight' => $this->joinNonEmpty([
                $detail->bl_detail_gross_weight,
                $detail->bl_detail_units,
            ], ' '),
            'measurement' => $detail->bl_detail_measurement,
            'delivery_agent' => Agent::find($detail->bl_detail_delivery_agent)?->name,
            'issue_place' => $detail->bl_detail_place_of_issue,
            'issue_date' => $this->formatDate($detail->bl_detail_date_of_issue),
            'original_bls' => $detail->bl_detail_original_bls,
        ];
    }

    /**
     * @param  array<int, string|null>  $parts
     */
    private function joinNonEmpty(array $parts, string $glue = "\n"): string
    {
        $kept = array_values(array_filter(
            array_map(static fn (?string $part): string => trim((string) $part), $parts),
            static fn (string $part): bool => $part !== '',
        ));

        return implode($glue, $kept);
    }

    private function formatDate(?string $date): ?string
    {
        if ($date === null || trim($date) === '') {
            return null;
        }

        $timestamp = strtotime($date);

        return $timestamp === false ? $date : date('j M Y', $timestamp);
    }
}
