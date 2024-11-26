import Bold from "@tiptap/extension-bold";
import {mdiFormatBold} from "@mdi/js";

window.FPRichEditor.registerPlugin('bold', {

    label: 'Bold',
    active: 'bold',
    buttons() {
      return [
          {
              name: 'bold',
              icon: mdiFormatBold,
              label: 'Bold',
              active: 'bold',
              shortcut: 'Mod-b',
              disabled: 'toggleBold',
              action: function(editor) {
                  editor.chain().focus().toggleBold().run();
              }
          }
      ]
    },

    tiptap: function() {
        return Bold;
    }
})
