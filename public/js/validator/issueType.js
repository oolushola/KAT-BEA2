$(function() {

    $('#saveIssueType').click(function(event) {
        event.preventDefault();
        processor('/issue-types')  
    })

    $('#updateIssueType').click(function(event) {
        event.preventDefault();
        $id = $('#id').val();
        processor('/issue-types/'+$id)  
    })

    const processor = (url) => {
        $issueCategory = $('#issueCategory').val();
        if($issueCategory == 0) {
            $('#messagePlaceholder').html('Issue category is required.').addClass('error');
            $('#issueCategory').focus();
            return false;
        }
        $issueType = $('#issueType').val();
        if($issueType == "") {
            $('#messagePlaceholder').html('Issue type is required.').addclass('error');
            $('#issueCategory').focus();
            return false;
        }
        $('#messagePlaceholder').html('<i class="icon-spinner2 spinner"></i> Please wait...').addClass('text-primary')
        $.post(url, $('#frmIssueType').serialize(), function(data) {
            if(data == "saved" | data == "updated") {
                $('#messagePlaceholder').html('Issue type '+data+' successfully.').addClass('text-success')
                window.location.href = '/issue-types';
            }
            else {
                if(data == "exists") {
                    $('#messagePlaceholder').html('<i class="icon-x">This issue type already exist for this category').addClass('error')
                }
            }
        })

    }
})