---
paths:
  - 'resources/pdf/**'
---

# Pdf

## BL Detail template is 60MB of uncompressed Illustrator artwork
resources/pdf/bl-detail-template.pdf is the pre-printed Apex B/L form and weighs ~60MB because Illustrator left content streams uncompressed (the logo alone is embedded at 1800x2170px for a 57x69pt placement). FPDI recompresses on import, so generated output is ~1.9MB — that is expected, not a bug. Replacing the file with a downsampled logo would shrink the repo copy considerably.
