import tiptap from "@tiptap/extension-strike";
import {mdiFormatStrikethrough} from "@mdi/js";

window.FPRichEditor.registerPlugin('strike', {

    label: 'Strike',
    active: 'strike',
    buttons() {
      return [
          {
              name: 'strike',
              icon: mdiFormatStrikethrough,
              active: 'strike',
              label: 'Strikethrough',
              shortcut: 'Mod-shift-s',
              disabled: 'toggleStrike',
              action: function(editor) {
                  editor.chain().focus().toggleStrike().run()
              }
          }
      ]
    },

    tiptap,
})
