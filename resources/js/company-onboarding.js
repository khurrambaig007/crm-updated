jQuery(document).ready(function ($) {
    var $onboarding = $('[data-company-onboarding]');

    if ($onboarding.length === 0) {
        return;
    }

    var steps = {
        1: {
            kicker: '01 — Company Profile',
            title: 'Tell us about your company',
            description: 'Add the core details people will see across your workspace.',
            hero: 'Build your company profile.',
            subtitle: 'Share a few details so we can set up your organization.',
        },
        2: {
            kicker: '02 — Person in Contact',
            title: 'Who should people contact?',
            description: 'Add the main point of contact for your organization.',
            hero: 'Add your contact details.',
            subtitle: 'Let your team know who to reach out to.',
        },
        3: {
            kicker: '03 — Message',
            title: 'Add a welcome message',
            description: 'Share a short message with people who use your workspace.',
            hero: 'Make the workspace yours.',
            subtitle: 'You can update these details at any time.',
        },
    };

    var currentStep = parseInt($onboarding.attr('data-start-step'), 10) || 1;
    var $form = $onboarding.find('[data-onboarding-form]');
    var $next = $onboarding.find('[data-onboarding-next]');
    var $previous = $onboarding.find('[data-onboarding-previous]');
    var $finish = $onboarding.find('[data-onboarding-finish]');

    function showStep(step) {
        currentStep = step;
        $onboarding.find('[data-onboarding-step]').addClass('hidden');
        $onboarding.find('[data-onboarding-step="' + step + '"]').removeClass('hidden');
        $onboarding.find('[data-step-indicator]')
            .removeClass('border-primary-200 bg-primary-50 font-semibold text-primary-900')
            .addClass('border-card-border bg-white text-topbar-muted');
        $onboarding.find('[data-step-indicator="' + step + '"]')
            .removeClass('border-card-border bg-white text-topbar-muted')
            .addClass('border-primary-200 bg-primary-50 font-semibold text-primary-900');

        var content = steps[step];
        $onboarding.find('[data-onboarding-kicker]').text(content.kicker);
        $onboarding.find('[data-onboarding-title]').text(content.title);
        $onboarding.find('[data-onboarding-description]').text(content.description);
        $onboarding.find('[data-onboarding-hero]').text(content.hero);
        $onboarding.find('[data-onboarding-subtitle]').text(content.subtitle);

        $previous.toggleClass('hidden', step === 1);
        $next.toggleClass('hidden', step === 3);
        $finish.toggleClass('hidden', step !== 3).toggleClass('inline-flex', step === 3);
    }

    $next.on('click', function () {
        var isValid = true;

        $onboarding.find('[data-onboarding-step="' + currentStep + '"] [required]').each(function () {
            if (!this.checkValidity()) {
                this.reportValidity();
                this.focus();
                isValid = false;

                return false;
            }
        });

        if (isValid) {
            showStep(Math.min(currentStep + 1, 3));
        }
    });

    $previous.on('click', function () {
        showStep(Math.max(currentStep - 1, 1));
    });

    $onboarding.find('#onboarding-logo').on('change', function () {
        var file = this.files && this.files[0];

        if (file) {
            $onboarding.find('[data-logo-name]').text(file.name);
        }
    });

    $form.on('submit', function () {
        $finish.prop('disabled', true).addClass('cursor-wait opacity-70');
    });

    showStep(Math.min(Math.max(currentStep, 1), 3));
    $onboarding.find('[data-onboarding-step="' + currentStep + '"] input').first().trigger('focus');
});
