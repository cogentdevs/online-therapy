export default function initializeBannerImageFields($) {
    const bannerType = $('[data-banner-type]');
    const bannerPosition = $('[data-banner-position]');
    const imageOneHelp = $('[data-banner-image-one-help]');
    const imageTwoGroup = $('[data-banner-image-two]');

    if (!bannerType.length || !bannerPosition.length || !imageTwoGroup.length) {
        return;
    }

    const imageTwoInput = imageTwoGroup.find('input[type="file"]');

    const updateImageFields = () => {
        const usesTwoImages = bannerType.val() === 'side-by-side';
        const usesCategoryDetailDimensions =
            bannerType.val() === 'full' &&
            bannerPosition.val() === 'category-detail-top-full';

        imageTwoGroup.toggleClass('d-none', !usesTwoImages);
        imageTwoInput.prop(
            'required',
            usesTwoImages && imageTwoInput.data('requiredWhenSideBySide') === true,
        );

        if (!usesTwoImages) {
            imageTwoInput.val('');
        }

        if (imageOneHelp.length) {
            imageOneHelp.text(
                usesCategoryDetailDimensions
                    ? imageOneHelp.data('categoryDetailHelp')
                    : imageOneHelp.data('defaultHelp'),
            );
        }
    };

    bannerType.add(bannerPosition).on('change', updateImageFields);
    updateImageFields();
}
