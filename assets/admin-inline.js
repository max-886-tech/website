
(function($){
  // Prefill Quick Edit from hidden inline data
  var $wp_inline_edit = inlineEditPost.edit;
  inlineEditPost.edit = function( id ){
    $wp_inline_edit.apply( this, arguments );
    var postId = 0;
    if ( typeof(id) == 'object' ) postId = parseInt(this.getId(id));
    if (!postId) return;
    var $box = $('#edit-' + postId);
    var $data = $('#en-inline-' + postId);
    if (!$data.length) return;
    $box.find('input[name="en_quick_demo"]').val($data.data('demo') || '');
    $box.find('input[name="en_quick_date"]').val($data.data('date') || '');
    $box.find('input[name="en_quick_qa"]').val($data.data('qa') || '');
  };

  // Bulk Edit handler - submit via AJAX to update selected posts
  $(document).on('click', '#bulk_edit', function(e){
    // collect post ids
    var $bulkRow = $('#bulk-edit');
    var ids = [];
    $bulkRow.find('#bulk-titles').children().each(function(){ ids.push($(this).attr('id').replace(/^(ttle)/, '')); });
    if (!ids.length) return;

    var demo = $bulkRow.find('input[name="en_bulk_demo"]').val();
    var date = $bulkRow.find('input[name="en_bulk_date"]').val();
    var qa   = $bulkRow.find('input[name="en_bulk_qa"]').val();

    $.post(enInline.ajax, {
      action: 'en_bulk_edit',
      nonce:  enInline.nonce,
      ids:    ids,
      demo:   demo,
      date:   date,
      qa:     qa
    }, function(resp){
      if (resp && resp.success) {
        location.reload();
      }
    });

  });
})(jQuery);

