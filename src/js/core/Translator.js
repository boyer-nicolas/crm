import fs from 'fs';
import PO from 'pofile';

class Translator
{
    constructor()
    {
        // PO.load('/translations/en.po', (err, po) =>
        // {
        //     if (err)
        //     {
        //         console.log(err);
        //         return;
        //     }
        //     this.po = po;
        // });

        // for (let key in this.po.items)
        // {
        //     if (this.po.items.hasOwnProperty(key))
        //     {
        //         let item = this.po.items[key];
        //         if (item.msgstr[0] != "")
        //         {
        //             console.log(item.msgid + ": " + item.msgstr[0]);
        //         }
        //     }
        // }

        var html = document.querySelector('html');
        var walker = document.createTreeWalker(html, NodeFilter.SHOW_TEXT);
        var node;
        while (node = walker.nextNode())
        {
            node.nodeValue = node.nodeValue.replace(/Clients/, 'Customers')
        }
    }
}

export default Translator;