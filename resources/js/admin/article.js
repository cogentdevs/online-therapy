import tinymce from 'tinymce/tinymce';
import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/models/dom';
import 'tinymce/plugins/autoresize';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/skins/ui/oxide/skin.min.css';

export default function initializeArticleEditor() {
    if (!document.querySelector('.article-editor')) {
        return;
    }

    tinymce.init({
        selector: '.article-editor',
        license_key: 'gpl',
        skin: false,
        content_css: false,
        content_style: 'body { font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; padding: 12px; }',
        plugins: 'autoresize code fullscreen link lists table',
        menubar: 'edit view insert format tools table',
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist blockquote | link table | removeformat code fullscreen',
        min_height: 420,
        autoresize_bottom_margin: 20,
        promotion: false,
        branding: false,
    });

    document.querySelectorAll('form').forEach((form) => {
        if (form.querySelector('.article-editor')) {
            form.addEventListener('submit', () => tinymce.triggerSave());
        }
    });
}
