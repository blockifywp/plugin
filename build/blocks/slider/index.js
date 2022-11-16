(()=>{"use strict";var t={1239:t=>{t.exports=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"blockify/slider","title":"Slider","description":"A simple, lightweight vanilla JS slider block.","icon":"slides","category":"blockify","textdomain":"blockify","keywords":["slider","carousel"],"supports":{"align":true,"inserter":true,"color":{"gradients":true},"spacing":{"margin":true,"padding":true,"blockGap":true},"__experimentalBorder":{"width":true,"style":true,"color":true,"radius":true,"__experimentalDefaultControls":{"width":true,"color":true}}},"attributes":{"clientId":{"type":"string"},"slideWidth":{"type":"string","default":"auto"},"autoplay":{"type":"boolean","default":false},"perView":{"type":"integer","default":3},"showArrows":{"type":"boolean","default":true},"showDots":{"type":"boolean","default":true},"style":{"type":"object","default":{"spacing":{"blockGap":"1em"}}}},"editorScript":"file:index.tsx","script":"file:script.tsx","editorStyle":"file:editor.scss","style":"file:style.scss","viewScript":"file:view.tsx"}')}},e={};function n(i){var r=e[i];if(void 0!==r)return r.exports;var o=e[i]={exports:{}};return t[i](o,o.exports,n),o.exports}(()=>{function t(){return t=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var i in n)Object.prototype.hasOwnProperty.call(n,i)&&(t[i]=n[i])}return t},t.apply(this,arguments)}const e=window.wp.element,i=window.wp.i18n,r=window.wp.blocks,o=window.wp.blockEditor,s=window.wp.components;function a(t){return a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},a(t)}function l(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function u(t,e){for(var n=0;n<e.length;n++){var i=e[n];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(t,i.key,i)}}function c(t,e,n){return e&&u(t.prototype,e),n&&u(t,n),t}function d(t){return d=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)},d(t)}function f(t,e){return f=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t},f(t,e)}function h(t,e){if(e&&("object"==typeof e||"function"==typeof e))return e;if(void 0!==e)throw new TypeError("Derived constructors may only return object or undefined");return function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t)}function p(t,e){for(;!Object.prototype.hasOwnProperty.call(t,e)&&null!==(t=d(t)););return t}function v(){return v="undefined"!=typeof Reflect&&Reflect.get?Reflect.get:function(t,e,n){var i=p(t,e);if(i){var r=Object.getOwnPropertyDescriptor(i,e);return r.get?r.get.call(arguments.length<3?t:n):r.value}},v.apply(this,arguments)}(0,r.registerBlockType)("blockify/slide",{apiVersion:2,title:(0,i.__)("Slide","blockify"),parent:["blockify/slider"],icon:"table-row-after",category:"blockify",keywords:["carousel","swipe","scroll"],supports:{color:{gradients:!0},spacing:{margin:!0,padding:!0,blockGap:!0},__experimentalBorder:{width:!0,style:!0,color:!0,radius:!0,__experimentalDefaultControls:{width:!0,color:!0}}},edit:()=>{const n=(0,o.useBlockProps)(),i=(0,o.useInnerBlocksProps)(n);return i.className=i.className+" glide__slide",(0,e.createElement)("div",t({},i,{className:n.className+" glide__slide"}))},save:()=>{const n=o.useBlockProps.save(),i=o.useInnerBlocksProps.save();return i.className=i.className+" glide__slide",(0,e.createElement)("div",t({},i,{className:n.className}))}});var m={type:"slider",startAt:0,perView:1,focusAt:0,gap:10,autoplay:!1,hoverpause:!0,keyboard:!0,bound:!1,swipeThreshold:80,dragThreshold:120,perSwipe:"",touchRatio:.5,touchAngle:45,animationDuration:400,rewind:!0,rewindDuration:800,animationTimingFunc:"cubic-bezier(.165, .840, .440, 1)",waitForTransition:!0,throttle:10,direction:"ltr",peek:0,cloningRatio:1,breakpoints:{},classes:{swipeable:"glide--swipeable",dragging:"glide--dragging",direction:{ltr:"glide--ltr",rtl:"glide--rtl"},type:{slider:"glide--slider",carousel:"glide--carousel"},slide:{clone:"glide__slide--clone",active:"glide__slide--active"},arrow:{disabled:"glide__arrow--disabled"},nav:{active:"glide__bullet--active"}}};function g(t){console.error("[Glide warn]: ".concat(t))}function y(t){return parseInt(t)}function b(t){return"string"==typeof t}function w(t){var e=a(t);return"function"===e||"object"===e&&!!t}function _(t){return"function"==typeof t}function k(t){return void 0===t}function S(t){return t.constructor===Array}function x(t,e,n){var i={};for(var r in e)_(e[r])?i[r]=e[r](t,i,n):g("Extension must be a function");for(var o in i)_(i[o].mount)&&i[o].mount();return i}function E(t,e,n){Object.defineProperty(t,e,n)}function O(t,e){var n=Object.assign({},t,e);return e.hasOwnProperty("classes")&&(n.classes=Object.assign({},t.classes,e.classes),e.classes.hasOwnProperty("direction")&&(n.classes.direction=Object.assign({},t.classes.direction,e.classes.direction)),e.classes.hasOwnProperty("type")&&(n.classes.type=Object.assign({},t.classes.type,e.classes.type)),e.classes.hasOwnProperty("slide")&&(n.classes.slide=Object.assign({},t.classes.slide,e.classes.slide)),e.classes.hasOwnProperty("arrow")&&(n.classes.arrow=Object.assign({},t.classes.arrow,e.classes.arrow)),e.classes.hasOwnProperty("nav")&&(n.classes.nav=Object.assign({},t.classes.nav,e.classes.nav))),e.hasOwnProperty("breakpoints")&&(n.breakpoints=Object.assign({},t.breakpoints,e.breakpoints)),n}var T=function(){function t(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};l(this,t),this.events=e,this.hop=e.hasOwnProperty}return c(t,[{key:"on",value:function(t,e){if(!S(t)){this.hop.call(this.events,t)||(this.events[t]=[]);var n=this.events[t].push(e)-1;return{remove:function(){delete this.events[t][n]}}}for(var i=0;i<t.length;i++)this.on(t[i],e)}},{key:"emit",value:function(t,e){if(S(t))for(var n=0;n<t.length;n++)this.emit(t[n],e);else this.hop.call(this.events,t)&&this.events[t].forEach((function(t){t(e||{})}))}}]),t}(),H=function(){function t(e){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};l(this,t),this._c={},this._t=[],this._e=new T,this.disabled=!1,this.selector=e,this.settings=O(m,n),this.index=this.settings.startAt}return c(t,[{key:"mount",value:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return this._e.emit("mount.before"),w(t)?this._c=x(this,t,this._e):g("You need to provide a object on `mount()`"),this._e.emit("mount.after"),this}},{key:"mutate",value:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[];return S(t)?this._t=t:g("You need to provide a array on `mutate()`"),this}},{key:"update",value:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return this.settings=O(this.settings,t),t.hasOwnProperty("startAt")&&(this.index=t.startAt),this._e.emit("update"),this}},{key:"go",value:function(t){return this._c.Run.make(t),this}},{key:"move",value:function(t){return this._c.Transition.disable(),this._c.Move.make(t),this}},{key:"destroy",value:function(){return this._e.emit("destroy"),this}},{key:"play",value:function(){var t=arguments.length>0&&void 0!==arguments[0]&&arguments[0];return t&&(this.settings.autoplay=t),this._e.emit("play"),this}},{key:"pause",value:function(){return this._e.emit("pause"),this}},{key:"disable",value:function(){return this.disabled=!0,this}},{key:"enable",value:function(){return this.disabled=!1,this}},{key:"on",value:function(t,e){return this._e.on(t,e),this}},{key:"isType",value:function(t){return this.settings.type===t}},{key:"settings",get:function(){return this._o},set:function(t){w(t)?this._o=t:g("Options must be an `object` instance.")}},{key:"index",get:function(){return this._i},set:function(t){this._i=y(t)}},{key:"type",get:function(){return this.settings.type}},{key:"disabled",get:function(){return this._d},set:function(t){this._d=!!t}}]),t}();function P(){return(new Date).getTime()}function A(t,e,n){var i,r,o,s,a=0;n||(n={});var l=function(){a=!1===n.leading?0:P(),i=null,s=t.apply(r,o),i||(r=o=null)},u=function(){var u=P();a||!1!==n.leading||(a=u);var c=e-(u-a);return r=this,o=arguments,c<=0||c>e?(i&&(clearTimeout(i),i=null),a=u,s=t.apply(r,o),i||(r=o=null)):i||!1===n.trailing||(i=setTimeout(l,c)),s};return u.cancel=function(){clearTimeout(i),a=0,i=r=o=null},u}var C={ltr:["marginLeft","marginRight"],rtl:["marginRight","marginLeft"]};function j(t){if(t&&t.parentNode){for(var e=t.parentNode.firstChild,n=[];e;e=e.nextSibling)1===e.nodeType&&e!==t&&n.push(e);return n}return[]}function N(t){return!!(t&&t instanceof window.HTMLElement)}var R='[data-glide-el="track"]',B=function(){function t(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};l(this,t),this.listeners=e}return c(t,[{key:"on",value:function(t,e,n){var i=arguments.length>3&&void 0!==arguments[3]&&arguments[3];b(t)&&(t=[t]);for(var r=0;r<t.length;r++)this.listeners[t[r]]=n,e.addEventListener(t[r],this.listeners[t[r]],i)}},{key:"off",value:function(t,e){var n=arguments.length>2&&void 0!==arguments[2]&&arguments[2];b(t)&&(t=[t]);for(var i=0;i<t.length;i++)e.removeEventListener(t[i],this.listeners[t[i]],n)}},{key:"destroy",value:function(){delete this.listeners}}]),t}(),D=["ltr","rtl"],M={">":"<","<":">","=":"="};function z(t,e){return{modify:function(t){return e.Direction.is("rtl")?-t:t}}}function L(t,e){return{modify:function(t){var n=Math.floor(t/e.Sizes.slideWidth);return t+e.Gaps.value*n}}}function I(t,e){return{modify:function(t){return t+e.Clones.grow/2}}}function V(t,e){return{modify:function(n){if(t.settings.focusAt>=0){var i=e.Peek.value;return w(i)?n-i.before:n-i}return n}}}function W(t,e){return{modify:function(n){var i=e.Gaps.value,r=e.Sizes.width,o=t.settings.focusAt,s=e.Sizes.slideWidth;return"center"===o?n-(r/2-s/2):n-s*o-i*o}}}var q=!1;try{var G=Object.defineProperty({},"passive",{get:function(){q=!0}});window.addEventListener("testPassive",null,G),window.removeEventListener("testPassive",null,G)}catch(t){}var F=q,Y=["touchstart","mousedown"],X=["touchmove","mousemove"],J=["touchend","touchcancel","mouseup","mouseleave"],K=["mousedown","mousemove","mouseup","mouseleave"],$='[data-glide-el^="controls"]',Q="".concat($,' [data-glide-dir*="<"]'),U="".concat($,' [data-glide-dir*=">"]');function Z(t){return w(t)?(e=t,Object.keys(e).sort().reduce((function(t,n){return t[n]=e[n],t[n],t}),{})):(g("Breakpoints option must be an object"),{});var e}var tt={Html:function(t,e,n){var i={mount:function(){this.root=t.selector,this.track=this.root.querySelector(R),this.collectSlides()},collectSlides:function(){this.slides=Array.prototype.slice.call(this.wrapper.children).filter((function(e){return!e.classList.contains(t.settings.classes.slide.clone)}))}};return E(i,"root",{get:function(){return i._r},set:function(t){b(t)&&(t=document.querySelector(t)),N(t)?i._r=t:g("Root element must be a existing Html node")}}),E(i,"track",{get:function(){return i._t},set:function(t){N(t)?i._t=t:g("Could not find track element. Please use ".concat(R," attribute."))}}),E(i,"wrapper",{get:function(){return i.track.children[0]}}),n.on("update",(function(){i.collectSlides()})),i},Translate:function(t,e,n){var i={set:function(n){var i=function(t,e,n){var i=[L,I,V,W].concat(t._t,[z]);return{mutate:function(n){for(var r=0;r<i.length;r++){var o=i[r];_(o)&&_(o().modify)?n=o(t,e,undefined).modify(n):g("Transformer should be a function that returns an object with `modify()` method")}return n}}}(t,e).mutate(n),r="translate3d(".concat(-1*i,"px, 0px, 0px)");e.Html.wrapper.style.mozTransform=r,e.Html.wrapper.style.webkitTransform=r,e.Html.wrapper.style.transform=r},remove:function(){e.Html.wrapper.style.transform=""},getStartIndex:function(){var n=e.Sizes.length,i=t.index,r=t.settings.perView;return e.Run.isOffset(">")||e.Run.isOffset("|>")?n+(i-r):(i+r)%n},getTravelDistance:function(){var n=e.Sizes.slideWidth*t.settings.perView;return e.Run.isOffset(">")||e.Run.isOffset("|>")?-1*n:n}};return n.on("move",(function(r){if(!t.isType("carousel")||!e.Run.isOffset())return i.set(r.movement);e.Transition.after((function(){n.emit("translate.jump"),i.set(e.Sizes.slideWidth*t.index)}));var o=e.Sizes.slideWidth*e.Translate.getStartIndex();return i.set(o-e.Translate.getTravelDistance())})),n.on("destroy",(function(){i.remove()})),i},Transition:function(t,e,n){var i=!1,r={compose:function(e){var n=t.settings;return i?"".concat(e," 0ms ").concat(n.animationTimingFunc):"".concat(e," ").concat(this.duration,"ms ").concat(n.animationTimingFunc)},set:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"transform";e.Html.wrapper.style.transition=this.compose(t)},remove:function(){e.Html.wrapper.style.transition=""},after:function(t){setTimeout((function(){t()}),this.duration)},enable:function(){i=!1,this.set()},disable:function(){i=!0,this.set()}};return E(r,"duration",{get:function(){var n=t.settings;return t.isType("slider")&&e.Run.offset?n.rewindDuration:n.animationDuration}}),n.on("move",(function(){r.set()})),n.on(["build.before","resize","translate.jump"],(function(){r.disable()})),n.on("run",(function(){r.enable()})),n.on("destroy",(function(){r.remove()})),r},Direction:function(t,e,n){var i={mount:function(){this.value=t.settings.direction},resolve:function(t){var e=t.slice(0,1);return this.is("rtl")?t.split(e).join(M[e]):t},is:function(t){return this.value===t},addClass:function(){e.Html.root.classList.add(t.settings.classes.direction[this.value])},removeClass:function(){e.Html.root.classList.remove(t.settings.classes.direction[this.value])}};return E(i,"value",{get:function(){return i._v},set:function(t){D.indexOf(t)>-1?i._v=t:g("Direction value must be `ltr` or `rtl`")}}),n.on(["destroy","update"],(function(){i.removeClass()})),n.on("update",(function(){i.mount()})),n.on(["build.before","update"],(function(){i.addClass()})),i},Peek:function(t,e,n){var i={mount:function(){this.value=t.settings.peek}};return E(i,"value",{get:function(){return i._v},set:function(t){w(t)?(t.before=y(t.before),t.after=y(t.after)):t=y(t),i._v=t}}),E(i,"reductor",{get:function(){var e=i.value,n=t.settings.perView;return w(e)?e.before/n+e.after/n:2*e/n}}),n.on(["resize","update"],(function(){i.mount()})),i},Sizes:function(t,e,n){var i={setupSlides:function(){for(var t="".concat(this.slideWidth,"px"),n=e.Html.slides,i=0;i<n.length;i++)n[i].style.width=t},setupWrapper:function(){e.Html.wrapper.style.width="".concat(this.wrapperSize,"px")},remove:function(){for(var t=e.Html.slides,n=0;n<t.length;n++)t[n].style.width="";e.Html.wrapper.style.width=""}};return E(i,"length",{get:function(){return e.Html.slides.length}}),E(i,"width",{get:function(){return e.Html.track.offsetWidth}}),E(i,"wrapperSize",{get:function(){return i.slideWidth*i.length+e.Gaps.grow+e.Clones.grow}}),E(i,"slideWidth",{get:function(){return i.width/t.settings.perView-e.Peek.reductor-e.Gaps.reductor}}),n.on(["build.before","resize","update"],(function(){i.setupSlides(),i.setupWrapper()})),n.on("destroy",(function(){i.remove()})),i},Gaps:function(t,e,n){var i={apply:function(t){for(var n=0,i=t.length;n<i;n++){var r=t[n].style,o=e.Direction.value;r[C[o][0]]=0!==n?"".concat(this.value/2,"px"):"",n!==t.length-1?r[C[o][1]]="".concat(this.value/2,"px"):r[C[o][1]]=""}},remove:function(t){for(var e=0,n=t.length;e<n;e++){var i=t[e].style;i.marginLeft="",i.marginRight=""}}};return E(i,"value",{get:function(){return y(t.settings.gap)}}),E(i,"grow",{get:function(){return i.value*e.Sizes.length}}),E(i,"reductor",{get:function(){var e=t.settings.perView;return i.value*(e-1)/e}}),n.on(["build.after","update"],A((function(){i.apply(e.Html.wrapper.children)}),30)),n.on("destroy",(function(){i.remove(e.Html.wrapper.children)})),i},Move:function(t,e,n){var i={mount:function(){this._o=0},make:function(){var t=this,i=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;this.offset=i,n.emit("move",{movement:this.value}),e.Transition.after((function(){n.emit("move.after",{movement:t.value})}))}};return E(i,"offset",{get:function(){return i._o},set:function(t){i._o=k(t)?0:y(t)}}),E(i,"translate",{get:function(){return e.Sizes.slideWidth*t.index}}),E(i,"value",{get:function(){var t=this.offset,n=this.translate;return e.Direction.is("rtl")?n+t:n-t}}),n.on(["build.before","run"],(function(){i.make()})),i},Clones:function(t,e,n){var i={mount:function(){this.items=[],t.isType("carousel")&&(this.items=this.collect())},collect:function(){var n=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],i=e.Html.slides,r=t.settings,o=r.perView,s=r.classes,a=r.cloningRatio;if(0!==i.length)for(var l=+!!t.settings.peek,u=o+l+Math.round(o/2),c=i.slice(0,u).reverse(),d=i.slice(-1*u),f=0;f<Math.max(a,Math.floor(o/i.length));f++){for(var h=0;h<c.length;h++){var p=c[h].cloneNode(!0);p.classList.add(s.slide.clone),n.push(p)}for(var v=0;v<d.length;v++){var m=d[v].cloneNode(!0);m.classList.add(s.slide.clone),n.unshift(m)}}return n},append:function(){for(var t=this.items,n=e.Html,i=n.wrapper,r=n.slides,o=Math.floor(t.length/2),s=t.slice(0,o).reverse(),a=t.slice(-1*o).reverse(),l="".concat(e.Sizes.slideWidth,"px"),u=0;u<a.length;u++)i.appendChild(a[u]);for(var c=0;c<s.length;c++)i.insertBefore(s[c],r[0]);for(var d=0;d<t.length;d++)t[d].style.width=l},remove:function(){for(var t=this.items,n=0;n<t.length;n++)e.Html.wrapper.removeChild(t[n])}};return E(i,"grow",{get:function(){return(e.Sizes.slideWidth+e.Gaps.value)*i.items.length}}),n.on("update",(function(){i.remove(),i.mount(),i.append()})),n.on("build.before",(function(){t.isType("carousel")&&i.append()})),n.on("destroy",(function(){i.remove()})),i},Resize:function(t,e,n){var i=new B,r={mount:function(){this.bind()},bind:function(){i.on("resize",window,A((function(){n.emit("resize")}),t.settings.throttle))},unbind:function(){i.off("resize",window)}};return n.on("destroy",(function(){r.unbind(),i.destroy()})),r},Build:function(t,e,n){var i={mount:function(){n.emit("build.before"),this.typeClass(),this.activeClass(),n.emit("build.after")},typeClass:function(){e.Html.root.classList.add(t.settings.classes.type[t.settings.type])},activeClass:function(){var n=t.settings.classes,i=e.Html.slides[t.index];i&&(i.classList.add(n.slide.active),j(i).forEach((function(t){t.classList.remove(n.slide.active)})))},removeClasses:function(){var n=t.settings.classes,i=n.type,r=n.slide;e.Html.root.classList.remove(i[t.settings.type]),e.Html.slides.forEach((function(t){t.classList.remove(r.active)}))}};return n.on(["destroy","update"],(function(){i.removeClasses()})),n.on(["resize","update"],(function(){i.mount()})),n.on("move.after",(function(){i.activeClass()})),i},Run:function(t,e,n){var i={mount:function(){this._o=!1},make:function(i){var r=this;t.disabled||(!t.settings.waitForTransition||t.disable(),this.move=i,n.emit("run.before",this.move),this.calculate(),n.emit("run",this.move),e.Transition.after((function(){r.isStart()&&n.emit("run.start",r.move),r.isEnd()&&n.emit("run.end",r.move),r.isOffset()&&(r._o=!1,n.emit("run.offset",r.move)),n.emit("run.after",r.move),t.enable()})))},calculate:function(){var e=this.move,n=this.length,r=e.steps,o=e.direction,s=1;if("="===o)return t.settings.bound&&y(r)>n?void(t.index=n):void(t.index=r);if(">"!==o||">"!==r)if("<"!==o||"<"!==r){if("|"===o&&(s=t.settings.perView||1),">"===o||"|"===o&&">"===r){var a=function(e){var n=t.index;return t.isType("carousel")?n+e:n+(e-n%e)}(s);return a>n&&(this._o=!0),void(t.index=function(e,n){var r=i.length;return e<=r?e:t.isType("carousel")?e-(r+1):t.settings.rewind?i.isBound()&&!i.isEnd()?r:0:i.isBound()?r:Math.floor(r/n)*n}(a,s))}if("<"===o||"|"===o&&"<"===r){var l=function(e){var n=t.index;return t.isType("carousel")?n-e:(Math.ceil(n/e)-1)*e}(s);return l<0&&(this._o=!0),void(t.index=function(e,n){var r=i.length;return e>=0?e:t.isType("carousel")?e+(r+1):t.settings.rewind?i.isBound()&&i.isStart()?r:Math.floor(r/n)*n:0}(l,s))}g("Invalid direction pattern [".concat(o).concat(r,"] has been used"))}else t.index=0;else t.index=n},isStart:function(){return t.index<=0},isEnd:function(){return t.index>=this.length},isOffset:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:void 0;return t?!!this._o&&("|>"===t?"|"===this.move.direction&&">"===this.move.steps:"|<"===t?"|"===this.move.direction&&"<"===this.move.steps:this.move.direction===t):this._o},isBound:function(){return t.isType("slider")&&"center"!==t.settings.focusAt&&t.settings.bound}};return E(i,"move",{get:function(){return this._m},set:function(t){var e=t.substr(1);this._m={direction:t.substr(0,1),steps:e?y(e)?y(e):e:0}}}),E(i,"length",{get:function(){var n=t.settings,i=e.Html.slides.length;return this.isBound()?i-1-(y(n.perView)-1)+y(n.focusAt):i-1}}),E(i,"offset",{get:function(){return this._o}}),i},Swipe:function(t,e,n){var i=new B,r=0,o=0,s=0,a=!1,l=!!F&&{passive:!0},u={mount:function(){this.bindSwipeStart()},start:function(e){if(!a&&!t.disabled){this.disable();var i=this.touches(e);r=null,o=y(i.pageX),s=y(i.pageY),this.bindSwipeMove(),this.bindSwipeEnd(),n.emit("swipe.start")}},move:function(i){if(!t.disabled){var a=t.settings,l=a.touchAngle,u=a.touchRatio,c=a.classes,d=this.touches(i),f=y(d.pageX)-o,h=y(d.pageY)-s,p=Math.abs(f<<2),v=Math.abs(h<<2),m=Math.sqrt(p+v),g=Math.sqrt(v);if(!(180*(r=Math.asin(g/m))/Math.PI<l))return!1;i.stopPropagation(),e.Move.make(f*parseFloat(u)),e.Html.root.classList.add(c.dragging),n.emit("swipe.move")}},end:function(i){if(!t.disabled){var s=t.settings,a=s.perSwipe,l=s.touchAngle,u=s.classes,c=this.touches(i),d=this.threshold(i),f=c.pageX-o,h=180*r/Math.PI;this.enable(),f>d&&h<l?e.Run.make(e.Direction.resolve("".concat(a,"<"))):f<-d&&h<l?e.Run.make(e.Direction.resolve("".concat(a,">"))):e.Move.make(),e.Html.root.classList.remove(u.dragging),this.unbindSwipeMove(),this.unbindSwipeEnd(),n.emit("swipe.end")}},bindSwipeStart:function(){var n=this,r=t.settings,o=r.swipeThreshold,s=r.dragThreshold;o&&i.on(Y[0],e.Html.wrapper,(function(t){n.start(t)}),l),s&&i.on(Y[1],e.Html.wrapper,(function(t){n.start(t)}),l)},unbindSwipeStart:function(){i.off(Y[0],e.Html.wrapper,l),i.off(Y[1],e.Html.wrapper,l)},bindSwipeMove:function(){var n=this;i.on(X,e.Html.wrapper,A((function(t){n.move(t)}),t.settings.throttle),l)},unbindSwipeMove:function(){i.off(X,e.Html.wrapper,l)},bindSwipeEnd:function(){var t=this;i.on(J,e.Html.wrapper,(function(e){t.end(e)}))},unbindSwipeEnd:function(){i.off(J,e.Html.wrapper)},touches:function(t){return K.indexOf(t.type)>-1?t:t.touches[0]||t.changedTouches[0]},threshold:function(e){var n=t.settings;return K.indexOf(e.type)>-1?n.dragThreshold:n.swipeThreshold},enable:function(){return a=!1,e.Transition.enable(),this},disable:function(){return a=!0,e.Transition.disable(),this}};return n.on("build.after",(function(){e.Html.root.classList.add(t.settings.classes.swipeable)})),n.on("destroy",(function(){u.unbindSwipeStart(),u.unbindSwipeMove(),u.unbindSwipeEnd(),i.destroy()})),u},Images:function(t,e,n){var i=new B,r={mount:function(){this.bind()},bind:function(){i.on("dragstart",e.Html.wrapper,this.dragstart)},unbind:function(){i.off("dragstart",e.Html.wrapper)},dragstart:function(t){t.preventDefault()}};return n.on("destroy",(function(){r.unbind(),i.destroy()})),r},Anchors:function(t,e,n){var i=new B,r=!1,o=!1,s={mount:function(){this._a=e.Html.wrapper.querySelectorAll("a"),this.bind()},bind:function(){i.on("click",e.Html.wrapper,this.click)},unbind:function(){i.off("click",e.Html.wrapper)},click:function(t){o&&(t.stopPropagation(),t.preventDefault())},detach:function(){if(o=!0,!r){for(var t=0;t<this.items.length;t++)this.items[t].draggable=!1;r=!0}return this},attach:function(){if(o=!1,r){for(var t=0;t<this.items.length;t++)this.items[t].draggable=!0;r=!1}return this}};return E(s,"items",{get:function(){return s._a}}),n.on("swipe.move",(function(){s.detach()})),n.on("swipe.end",(function(){e.Transition.after((function(){s.attach()}))})),n.on("destroy",(function(){s.attach(),s.unbind(),i.destroy()})),s},Controls:function(t,e,n){var i=new B,r=!!F&&{passive:!0},o={mount:function(){this._n=e.Html.root.querySelectorAll('[data-glide-el="controls[nav]"]'),this._c=e.Html.root.querySelectorAll($),this._arrowControls={previous:e.Html.root.querySelectorAll(Q),next:e.Html.root.querySelectorAll(U)},this.addBindings()},setActive:function(){for(var t=0;t<this._n.length;t++)this.addClass(this._n[t].children)},removeActive:function(){for(var t=0;t<this._n.length;t++)this.removeClass(this._n[t].children)},addClass:function(e){var n=t.settings,i=e[t.index];i&&i&&(i.classList.add(n.classes.nav.active),j(i).forEach((function(t){t.classList.remove(n.classes.nav.active)})))},removeClass:function(e){var n=e[t.index];n&&n.classList.remove(t.settings.classes.nav.active)},setArrowState:function(){if(!t.settings.rewind){var n=o._arrowControls.next,i=o._arrowControls.previous;this.resetArrowState(n,i),0===t.index&&this.disableArrow(i),t.index===e.Run.length&&this.disableArrow(n)}},resetArrowState:function(){for(var e=t.settings,n=arguments.length,i=new Array(n),r=0;r<n;r++)i[r]=arguments[r];i.forEach((function(t){t.forEach((function(t){t.classList.remove(e.classes.arrow.disabled)}))}))},disableArrow:function(){for(var e=t.settings,n=arguments.length,i=new Array(n),r=0;r<n;r++)i[r]=arguments[r];i.forEach((function(t){t.forEach((function(t){t.classList.add(e.classes.arrow.disabled)}))}))},addBindings:function(){for(var t=0;t<this._c.length;t++)this.bind(this._c[t].children)},removeBindings:function(){for(var t=0;t<this._c.length;t++)this.unbind(this._c[t].children)},bind:function(t){for(var e=0;e<t.length;e++)i.on("click",t[e],this.click),i.on("touchstart",t[e],this.click,r)},unbind:function(t){for(var e=0;e<t.length;e++)i.off(["click","touchstart"],t[e])},click:function(t){F||"touchstart"!==t.type||t.preventDefault();var n=t.currentTarget.getAttribute("data-glide-dir");e.Run.make(e.Direction.resolve(n))}};return E(o,"items",{get:function(){return o._c}}),n.on(["mount.after","move.after"],(function(){o.setActive()})),n.on(["mount.after","run"],(function(){o.setArrowState()})),n.on("destroy",(function(){o.removeBindings(),o.removeActive(),i.destroy()})),o},Keyboard:function(t,e,n){var i=new B,r={mount:function(){t.settings.keyboard&&this.bind()},bind:function(){i.on("keyup",document,this.press)},unbind:function(){i.off("keyup",document)},press:function(n){var i=t.settings.perSwipe;39===n.keyCode&&e.Run.make(e.Direction.resolve("".concat(i,">"))),37===n.keyCode&&e.Run.make(e.Direction.resolve("".concat(i,"<")))}};return n.on(["destroy","update"],(function(){r.unbind()})),n.on("update",(function(){r.mount()})),n.on("destroy",(function(){i.destroy()})),r},Autoplay:function(t,e,n){var i=new B,r={mount:function(){this.enable(),this.start(),t.settings.hoverpause&&this.bind()},enable:function(){this._e=!0},disable:function(){this._e=!1},start:function(){var i=this;this._e&&(this.enable(),t.settings.autoplay&&k(this._i)&&(this._i=setInterval((function(){i.stop(),e.Run.make(">"),i.start(),n.emit("autoplay")}),this.time)))},stop:function(){this._i=clearInterval(this._i)},bind:function(){var t=this;i.on("mouseover",e.Html.root,(function(){t._e&&t.stop()})),i.on("mouseout",e.Html.root,(function(){t._e&&t.start()}))},unbind:function(){i.off(["mouseover","mouseout"],e.Html.root)}};return E(r,"time",{get:function(){return y(e.Html.slides[t.index].getAttribute("data-glide-autoplay")||t.settings.autoplay)}}),n.on(["destroy","update"],(function(){r.unbind()})),n.on(["run.before","swipe.start","update"],(function(){r.stop()})),n.on(["pause","destroy"],(function(){r.disable(),r.stop()})),n.on(["run.after","swipe.end"],(function(){r.start()})),n.on(["play"],(function(){r.enable(),r.start()})),n.on("update",(function(){r.mount()})),n.on("destroy",(function(){i.destroy()})),r},Breakpoints:function(t,e,n){var i=new B,r=t.settings,o=Z(r.breakpoints),s=Object.assign({},r),a={match:function(t){if(void 0!==window.matchMedia)for(var e in t)if(t.hasOwnProperty(e)&&window.matchMedia("(max-width: ".concat(e,"px)")).matches)return t[e];return s}};return Object.assign(r,a.match(o)),i.on("resize",window,A((function(){t.settings=O(r,a.match(o))}),t.settings.throttle)),n.on("update",(function(){o=Z(o),s=Object.assign({},r)})),n.on("destroy",(function(){i.off("resize",window)})),a}},et=function(t){!function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&f(t,e)}(r,t);var e,n,i=(e=r,n=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(t){return!1}}(),function(){var t,i=d(e);if(n){var r=d(this).constructor;t=Reflect.construct(i,arguments,r)}else t=i.apply(this,arguments);return h(this,t)});function r(){return l(this,r),i.apply(this,arguments)}return c(r,[{key:"mount",value:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};return v(d(r.prototype),"mount",this).call(this,Object.assign({},tt,t))}}]),r}(H);const nt=window.wp.data,it=n(1239);(0,r.registerBlockType)(it,{edit:n=>{var r,a;const{attributes:l,clientId:u,setAttributes:c}=n,{showArrows:d,showDots:f}=l,h=(0,o.useBlockProps)(),[p]=(0,e.useState)((0,nt.select)("core/block-editor").getBlocksByClientId(u)[0]),v=(0,o.useInnerBlocksProps)(h,{allowedBlocks:["blockify/slide"],template:[["blockify/slide",[],[["core/paragraph"]]],["blockify/slide",[],[["core/paragraph"]]],["blockify/slide",[],[["core/paragraph"]]]]}),m={type:"carousel",perView:null!==(r=parseInt(null==l?void 0:l.perView))&&void 0!==r?r:3,gap:0,breakpoints:{782:{perView:parseInt(null==l?void 0:l.perView)>1?2:1},512:{perView:1}}},[g,y]=(0,e.useState)({});return(0,e.useEffect)((()=>{if(!document.querySelector(".glide")){const t=document.getElementsByClassName("edit-site-visual-editor__editor-canvas")[0];return void(t&&console.log(t))}let t=new et("#block-"+u,m);y(t),t.mount()}),[y]),c({clientId:u}),(0,e.createElement)("div",t({},h,{className:h.className+" glide"}),(0,e.createElement)(o.InspectorControls,null,(0,e.createElement)(s.PanelBody,{title:(0,i.__)("Slider Settings","blockify"),initialOpen:!0,className:"blockify-slider-settings"},(0,e.createElement)(s.PanelRow,null,(0,e.createElement)(s.__experimentalNumberControl,{label:(0,i.__)("Per View","blockify"),help:(0,i.__)("Number of slides to display in the viewport on desktop.","blockify"),onChange:t=>{c({perView:parseInt(t)}),"function"==typeof g.update&&g.update({perView:parseInt(t),breakpoints:{782:{perView:parseInt(t)>1?2:1}}})},step:1,shiftStep:1,isDragEnabled:!1,isShiftStepEnabled:!1,value:null!==(a=parseInt(null==l?void 0:l.perView))&&void 0!==a?a:3,min:1,max:6,require:!0}),(0,e.createElement)("br",null)),(0,e.createElement)(s.PanelRow,null,(0,e.createElement)(s.ToggleControl,{label:(0,i.__)("Show arrows","blockify"),checked:null!=d&&d,onChange:()=>c({showArrows:!d})})),(0,e.createElement)(s.PanelRow,null,(0,e.createElement)(s.ToggleControl,{label:(0,i.__)("Show dots","blockify"),checked:null!=f&&f,onChange:()=>c({showDots:!f})})))),(0,e.createElement)("div",{className:"glide__track","data-glide-el":"track"},(0,e.createElement)("div",{className:"glide__slides"},v.children)),d&&(0,e.createElement)("div",{className:"glide__arrows","data-glide-el":"controls"},(0,e.createElement)("button",{className:"glide__arrow glide__arrow--left","data-glide-dir":"<"},(0,i.__)("Previous","blockify")),(0,e.createElement)("button",{className:"glide__arrow glide__arrow--right","data-glide-dir":">"},(0,i.__)("Next","blockify"))),f&&(0,e.createElement)("div",{className:"glide__bullets","data-glide-el":"controls[nav]"},Object.keys(p.innerBlocks).map(((t,n)=>(0,e.createElement)("button",{className:"glide__bullet","data-glide-dir":n})))))},save:n=>{var i;let{attributes:r}=n;const s=o.useBlockProps.save(),a=o.useInnerBlocksProps.save(),{clientId:l,showArrows:u,showDots:c}=r;return(0,e.createElement)("div",t({},s,{className:s.className+" glide",id:null!==(i=null==s?void 0:s.id)&&void 0!==i?i:"block-"+l}),(0,e.createElement)("div",{"data-glide-el":"track",className:"glide__track"},(0,e.createElement)("div",{className:"glide__slides"},a.children)),u&&(0,e.createElement)("div",{className:"glide__arrows","data-glide-el":"controls"},(0,e.createElement)("button",{className:"glide__arrow glide__arrow--left","data-glide-dir":"<"},"‹"),(0,e.createElement)("button",{className:"glide__arrow glide__arrow--right","data-glide-dir":">"},"›")),c&&(0,e.createElement)("div",{className:"glide__bullets","data-glide-el":"controls[nav]"},[1,2,3].map(((t,n)=>(0,e.createElement)("button",{className:"glide__bullet","data-glide-dir":n})))))}})})()})();