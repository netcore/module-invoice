!function(e){function a(r){if(t[r])return t[r].exports;var n=t[r]={i:r,l:!1,exports:{}};return e[r].call(n.exports,n,n.exports,a),n.l=!0,n.exports}var t={};a.m=e,a.c=t,a.d=function(e,t,r){a.o(e,t)||Object.defineProperty(e,t,{configurable:!1,enumerable:!0,get:r})},a.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return a.d(t,"a",t),t},a.o=function(e,a){return Object.prototype.hasOwnProperty.call(e,a)},a.p="",a(a.s=0)}([function(e,a,t){e.exports=t(1)},function(e,a,t){"use strict";init.push(function(){var e=$("#invoices-datatable");if(e.length){var a=[{data:"invoice_nr",name:"invoice_nr",orderable:!0,searchable:!0},{data:"created_at",name:"created_at",orderable:!0,searchable:!0}];$.each(enabledRelations,function(e,t){a.push({data:t.table.d_data,name:t.table.d_name,orderable:t.table.sortable,searchable:t.table.searchable})}),a.push({data:"total_without_vat",name:"total_without_vat",orderable:!0,searchable:!0}),a.push({data:"total_with_vat",name:"total_with_vat",orderable:!0,searchable:!0}),a.push({data:"actions",orderable:!1,searchable:!1,className:"text-right"}),e.dataTable({processing:!0,serverSide:!0,ajax:$(e).data("ajax"),responsive:!0,columns:a}),e.parent().parent().find("input[type=search]").attr("placeholder","Search..."),e.parent().parent().find(".table-caption").html("Invoices")}})}]);