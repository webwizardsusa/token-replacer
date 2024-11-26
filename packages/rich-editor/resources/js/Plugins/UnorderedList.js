import unorderedList from "@tiptap/extension-bullet-list";
import listItem from '@tiptap/extension-list-item';
import {mdiFormatListBulleted} from "@mdi/js";
import orderedList from "@tiptap/extension-ordered-list";

window.FPRichEditor.registerPlugin('unorderedList', {

    buttons() {
        return [
            {
                name: 'unorderedList',
                icon: mdiFormatListBulleted,
                active: 'bulletList',
                label: 'Unordered list',
                action: function(editor) {
                    editor.chain().focus().toggleBulletList().run()
                }
            }
        ]
    },

    dependencies: ['listItem'],
    tiptap: unorderedList,
})
