export default function initializeMagazineLanguageOptions($) {
    const relatedSelects = $('[data-magazine-language-options]');

    if (relatedSelects.length === 0) {
        return;
    }

    const filterOptions = () => {
        const selectedLanguage = 'en';

        relatedSelects.each(function filterRelatedSelect() {
            const select = $(this);

            select.find('option[data-language]').each(function filterOption() {
                const option = $(this);
                const isAvailable = option.data('language') === selectedLanguage;

                option.prop('disabled', !isAvailable);

                if (!isAvailable) {
                    option.prop('selected', false);
                }
            });

            select.trigger('change.select2');
        });
    };

    filterOptions();
}
