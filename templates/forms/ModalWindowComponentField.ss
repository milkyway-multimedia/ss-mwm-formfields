<% if $TriggerButton %>
    $TriggerButton.FieldHolder
<% else_if $Trigger %>
    <div class="modal-init $TriggerClasses">
        <a href="#$ID" data-toggle="modal" $TriggerAttributesHTML>$Trigger</a>
    </div>
<% end_if %>

<div <% if $AttributesHTML %>$AttributesHTML<% else %>data-modal="true" id="$ID"
                                                                        class="<% if $StyleClasses %>$StyleClasses<% else %>modal fade<% end_if %>"<% end_if %>>
    <div class="modal-dialog<% if $DialogClasses %> $DialogClasses<% end_if %>">
        <div class="modal-content">
            <% if not $NoHeader %>
                <div class="modal-header">
                    <% if not $NoClose %>
                        <button type="button" class="close modal-dismiss" data-dismiss="modal"
                                aria-hidden="true">&times;</button><% end_if %>
                    <h4 id="$ID-Title" role="title">$Title</h4>
                </div>
            <% end_if %>
            <div class="modal-body" id="$ID-Content" role="content">


                <% if $FieldList %>
                    <% loop $FieldList %>
                        $FieldHolder
                    <% end_loop %>
                <% else_if $IframeSrc %>
                    <iframe src="$IframeSrc" class="modal-iframe" frameborder="0">
                        <% _t('NO_IFRAME_SUPPORT', 'Your browser does not support iframes. Please upgrade or use a different browser') %>
                    </iframe>
                <% else %>
                    $Body
                <% end_if %>

            </div>
            <% if not $NoFooter %>
                <div class="modal-footer">
                    <% if $FooterFields %>
                        <% loop $FooterFields %>
                            $FieldHolder
                        <% end_loop %>
                    <% end_if %>
                    <% if not $NoClose %><a href="#" class="btn btn-close"
                                            data-dismiss="modal">Close</a><% end_if %>
                </div>
            <% end_if %>
        </div>
    </div>
</div>