import orderedList from "@tiptap/extension-ordered-list";
import listItem from '@tiptap/extension-list-item';
import {mdiFormatListNumbered} from "@mdi/js";

window.FPRichEditor.registerPlugin('orderedList', {

    label: 'Ordered List',
    active: 'strike',
    buttons() {
        return [
            {
                name: 'orderedList',
                icon: mdiFormatListNumbered,
                active: 'orderedList',
                label: 'Ordered list',
                action: function(editor) {
                    editor.chain().focus().toggleOrderedList().run()
                }
            }
        ]
    },

    dependencies: ['listItem'],
    tiptap: orderedList,
})
