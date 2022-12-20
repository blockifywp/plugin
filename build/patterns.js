!function(){"use strict";var e,t,n=window.wp.i18n;const o=null!==(e=null===(t=window)||void 0===t?void 0:t.blockifyPatternEditor)&&void 0!==e?e:{};document.addEventListener("DOMContentLoaded",(()=>{var e,t;const l=document.getElementById("adv-settings"),i=document.createElement("fieldset"),a=document.createElement("legend"),r=document.createElement("br"),d=document.createElement("label"),c=document.createElement("label"),s=document.createElement("input"),p=document.createElement("input");a.innerHTML=(0,n.__)("Export Paths","blockify-pro"),d.innerHTML=(0,n.__)("Pattern Export Path","blockify-pro"),d.htmlFor="blockify-pattern-export-path",s.classList.add("blockify-patterns-export-path"),s.type="text",s.placeholder=(0,n.__)("themes/child-theme/patterns","blockify-pro"),s.value=null!==(e=null==o?void 0:o.patternDir)&&void 0!==e?e:"",s.style.marginLeft="5px",c.innerHTML=(0,n.__)("Image Export Path","blockify-pro"),c.htmlFor="blockify-pattern-image-export-path",p.classList.add("blockify-patterns-export-path"),p.type="text",p.placeholder=(0,n.__)("themes/child-theme/images","blockify-pro"),p.value=null!==(t=null==o?void 0:o.imgDir)&&void 0!==t?t:"",p.style.marginLeft="5px",r.style.marginBottom="10px",i.appendChild(a),i.appendChild(d),i.appendChild(s),i.appendChild(r),i.appendChild(c),i.appendChild(p),l.before(i)})),document.addEventListener("DOMContentLoaded",(()=>{const e=document.createElement("span"),t=document.getElementById("post-query-submit");e.classList.add("button","blockify-patterns-grid-button"),e.innerHTML=(0,n.__)("Toggle Preview","blockify-pro"),t.after(e),e.addEventListener("click",(()=>{var e;document.body.classList.toggle("show-patterns");const t=(null==o?void 0:o.restUrl)+"wp/v2/users/"+(null==o?void 0:o.currentUser);fetch(t,{method:"POST",credentials:"same-origin",body:JSON.stringify({meta:{blockify_show_patterns:document.body.classList.contains("show-patterns")?"1":"0"}}),headers:{"X-WP-Nonce":null!==(e=null==o?void 0:o.nonce)&&void 0!==e?e:"","Content-Type":"application/json"}}).then((e=>e.json())).then((e=>{console.log(e)}))}))})),document.addEventListener("DOMContentLoaded",(()=>{const e=document.createElement("a"),t=document.getElementsByClassName("page-title-action").item(0),l=(null==o?void 0:o.adminUrl)+"admin-post.php?action=blockify_export_patterns";e.classList.add("blockify-patterns-import","page-title-action"),e.innerHTML=(0,n.__)("Export Patterns","blockify-pro"),t.after(e),e.addEventListener("click",(()=>{confirm((0,n.__)("WARNING: This will overwrite all block pattern HTML files in the following theme:"+(null==o?void 0:o.stylesheet)+". We recommend creating your own child theme. Would you like to continue?","blockify-pro"))&&(window.location.href=l)}))})),document.addEventListener("DOMContentLoaded",(()=>{const e=document.createElement("a"),t=document.getElementsByClassName("page-title-action").item(0),l=(null==o?void 0:o.adminUrl)+"admin-post.php?action=blockify_import_patterns";e.classList.add("blockify-patterns-import","page-title-action"),e.innerHTML=(0,n.__)("Import Patterns","blockify-pro"),e.onclick=()=>{var e,t,i;return!!confirm((0,n.__)("Import all registered block patterns? (Active theme: "+(null==o||null===(e=o.stylesheet)||void 0===e||null===(t=e.charAt(0))||void 0===t?void 0:t.toUpperCase())+(null==o||null===(i=o.stylesheet)||void 0===i?void 0:i.slice(1))+")","blockify-pro"))&&(window.location.href=l)},t.after(e)}))}();