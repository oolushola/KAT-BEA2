(function(d){function e(f){var g=d("");try{g=d(f).clone()}catch(h){g=d("<span />").html(f)}return g}function b(i,h){var g=d.Deferred();try{setTimeout(function(){i.focus();try{if(!i.document.execCommand("print",false,null)){i.print()}}catch(j){i.print()}i.close();g.resolve()},h)}catch(f){g.reject(f)}return g}function a(g,h){var f=window.open();f.document.write(g);f.document.close();return b(f,h)}function c(f){return !!(typeof Node==="object"?f instanceof Node:f&&typeof f==="object"&&typeof f.nodeType==="number"&&typeof f.nodeName==="string")}d.print=d.fn.print=function(){var r,l,q=this;if(q instanceof d){q=q.get(0)}if(c(q)){l=d(q);if(arguments.length>0){r=arguments[0]}}else{if(arguments.length>0){l=d(arguments[0]);if(c(l[0])){if(arguments.length>1){r=arguments[1]}}else{r=arguments[0];l=d("html")}}else{l=d("html")}}var g={globalStyles:true,mediaPrint:false,stylesheet:null,noPrintSelector:".no-print",iframe:true,append:null,prepend:null,manuallyCopyFormValues:true,deferred:d.Deferred(),timeout:250};r=d.extend({},g,(r||{}));var i=d("");if(r.globalStyles){i=d("style, link, meta, title")}else{if(r.mediaPrint){i=d("link[media=print]")}}if(r.stylesheet){i=d.merge(i,d('<link rel="stylesheet" href="'+r.stylesheet+'">'))}var f=l.clone();f=d("<span/>").append(f);f.find(r.noPrintSelector).remove();f.append(i.clone());f.append(e(r.append));f.prepend(e(r.prepend));if(r.manuallyCopyFormValues){f.find("input").each(function(){var s=d(this);if(s.is("[type='radio']")||s.is("[type='checkbox']")){if(s.prop("checked")){s.attr("checked","checked")}}else{s.attr("value",s.val())}});f.find("select").each(function(){var s=d(this);s.find(":selected").attr("selected","selected")});f.find("textarea").each(function(){var s=d(this);s.text(s.val())})}var k=f.html();try{r.deferred.notify("generated_markup",k,f)}catch(h){console.warn("Error notifying deferred",h)}f.remove();if(r.iframe){try{var p=d(r.iframe+"");var o=p.length;if(o===0){p=d('<iframe height="0" width="0" border="0" wmode="Opaque"/>').prependTo("body").css({position:"absolute",top:-999,left:-999})}var n,m;n=p.get(0);n=n.contentWindow||n.contentDocument||n;m=n.document||n.contentDocument||n;m.open();m.write(k);m.close();b(n,r.timeout).done(function(){setTimeout(function(){if(o===0){p.remove()}},100)}).fail(function(s){console.error("Failed to print from iframe",s);a(k,r.timeout)}).always(function(){try{r.deferred.resolve()}catch(s){console.warn("Error notifying deferred",s)}})}catch(j){console.error("Failed to print from iframe",j.stack,j.message);a(k,r.timeout).always(function(){try{r.deferred.resolve()}catch(s){console.warn("Error notifying deferred",s)}})}}else{a(k,r.timeout).always(function(){try{r.deferred.resolve()}catch(s){console.warn("Error notifying deferred",s)}})}return this}})(jQuery);