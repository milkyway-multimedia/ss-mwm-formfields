<% if not $bottomOnly %>
    <% if $modalInitButton %>
        $modalInitButton.FieldHolder
    <% else_if $modalInit %>
		<div class="modal-init $modalInitClasses">
			<a href="#<% if $modalID %>$modalID<% else %>$modalName<% end_if %>" data-toggle="modal" $modalInitAttributes>$modalInit</a>
		</div>
    <% end_if %>
    <div <% if $modalAttributes %>$modalAttributes<% else %>data-modal="true"id="<% if $modalID %>$modalID<% else %>{$modalName}-Modal<% end_if %>" class="<% if $modalClasses %>$modalClasses<% else %>modal fade<% end_if %>"<% end_if %>>
    <div class="modal-dialog<% if $modalDialogClasses %> $modalDialogClasses<% end_if %>">
	<div class="modal-content">
    <% if not $modalNoHeader %>
			<div class="modal-header">
				<% if not $modalNoClose %><button type="button" class="close modal-dismiss" data-dismiss="modal" aria-hidden="true">&times;</button><% end_if %>
				<h4 id="<% if $modalID %>$modalID<% else %>$modalName<% end_if %>-Title" role="title">$modalTitle</h4>
			</div>
    <% end_if %>
	<div class="modal-body" id="<% if $modalID %>$modalID<% else %>$modalName<% end_if %>-Content" role="content">
<% end_if %>

<% if $modalFields %>
    <% loop $modalFields %>
        $FieldHolder
    <% end_loop %>
<% else_if $modalIframe %>
		<iframe src="$modalIframe" class="modal-iframe" frameborder="0">
            <% _t('NO_IFRAME_SUPPORT', 'Your browser does not support iframes. Please upgrade or use a different browser') %>
		</iframe>
<% else %>
    $modalBody
<% end_if %>

<% if not $topOnly %>
	</div>
    <% if not $modalNoFooter %>
			<div class="modal-footer">
                <% if $modalFooterFields %>
                    <% loop $modalFooterFields %>
                        $FieldHolder
                    <% end_loop %>
                <% end_if %>
                <% if not $modalNoClose %><a href="#" class="btn btn-close" data-dismiss="modal">Close</a><% end_if %>
			</div>
    <% end_if %>
	</div>
	</div>
	</div>
<% end_if %>