var h=Object.defineProperty;var _=(e,t,s)=>t in e?h(e,t,{enumerable:!0,configurable:!0,writable:!0,value:s}):e[t]=s;var i=(e,t,s)=>_(e,typeof t!="symbol"?t+"":t,s);var n=class{constructor(){i(this,"_inline",!1);i(this,"_selected",!1);i(this,"_types",[]);i(this,"_options",{});i(this,"_collection",null)}then(t){return this._callback=t,this}types(t){return this._types=t,this}attributes(t){return this._attributes=t,this}selected(t){return this._selected=t,this}collection(t){return this._collection=t,this}inline(t=!0){return this._inline=t,this}options(t){return this._options=t,this}},l=class{constructor(){this._bound=!1}handleResults(t){this._intent&&(t.length?this._intent._callback(t):this._intent._callback(null),this._intent=null)}show(){return this._bound||(Livewire.on("filapress-media-browser-results",t=>{this.handleResults(t)}),this._bound=!0),this._intent=new n,setTimeout(()=>{let t={types:this._intent._types,selected:this._intent._selected,inline:this._intent._inline,attributes:this._intent._attributes,collection:this._intent._collection,...this._intent._options};Livewire.dispatch("open-filapress-media-browser",{options:t})}),this._intent}};window.FPMediaBrowser=new l;setTimeout(()=>{},250);