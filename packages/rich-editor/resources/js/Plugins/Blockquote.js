import Blockquote from "@tiptap/extension-blockquote";
import {mdiFormatQuoteClose} from "@mdi/js";

window.FPRichEditor.registerPlugin('blockquote', {

    label: 'Blockquote',
    active: 'blockquote',
    buttons() {
      return [
          {
              name: 'blockquote',
              label: 'Blockquote',
              shortcut: 'Mod-shift-b',
              icon: mdiFormatQuoteClose,
              active: 'blockquote',
              disabled: 'toggleBlockquote',
              action: function(editor) {
                  editor.chain().focus().toggleBlockquote().run();
              }
          }
      ]
    },

    tiptap: Blockquote,
})
