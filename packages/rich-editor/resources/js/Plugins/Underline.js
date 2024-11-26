import Underline from "@tiptap/extension-underline";
import {mdiFormatUnderline} from "@mdi/js";

window.FPRichEditor.registerPlugin('underline', {

    label: 'Underline',
    active: 'underline',
    buttons() {
      return [
          {
              name: 'underline',
              icon: mdiFormatUnderline,
              active: 'underline',
              label: 'Underline',
              shortcut: 'Mod-u',
              'disabled': 'toggleUnderline',
              action: function(editor) {
                  editor.chain().focus().toggleUnderline().run();
              }
          }
      ]
    },

    tiptap: Underline,
})
