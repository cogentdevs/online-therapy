export default function initializeCorePageSlug($) {
    const pageSelect = $('[data-core-page-select]');
    const slugInput = $('[data-core-page-slug]');

    if (pageSelect.length === 0 || slugInput.length === 0) {
        return;
    }

    const applySelectedSlug = () => {
        const slug = pageSelect.find(':selected').data('page-slug');
        slugInput.val(slug ?? '');
    };

    pageSelect.on('change', applySelectedSlug);

    if (!slugInput.val()) {
        applySelectedSlug();
    }
}
