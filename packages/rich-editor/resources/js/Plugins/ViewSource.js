import {mdiXml} from "@mdi/js";
import {createPopper} from '@popperjs/core';

window.FPRichEditor.registerPlugin('view_source', {

    buttons: function () {
        return [
            {
                name: 'view_source',
                icon: mdiXml,
                active: false,
                label: 'View Source',
                action: function (editor, component) {
                    component.openModal('view-source', {source: editor.getHTML()});

                }
            }
        ];
    },

})
