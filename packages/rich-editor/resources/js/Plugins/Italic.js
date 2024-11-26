import Italic from "@tiptap/extension-italic";
import {mdiFormatItalic} from "@mdi/js";

window.FPRichEditor.registerPlugin('italic', {

    label: 'Italic',
    active: 'italic',
    buttons() {
      return [
          {
              name: 'italic',
              icon: mdiFormatItalic,
              active: 'italic',
              label: 'Italicize',
              shortcut: 'Mod-i',
              disabled: 'toggleItalic',
              action: function(editor) {
                  editor.chain().focus().toggleItalic().run();
              }
          }
      ]
    },

    tiptap: Italic,
})
