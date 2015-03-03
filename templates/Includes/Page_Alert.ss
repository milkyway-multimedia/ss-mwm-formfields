<% if not $bottomOnly %>
	<div id="$alertName-Alert" class="alert<% if $alertType %> $alertType<% end_if %><% if $alertDismiss%> alert-dismissable<% end_if %>">
<% end_if %>

<% if $alertIframe %>
		<iframe src="$alertIframe" class="modal-iframe" frameborder="0">
            <% _t('NO_IFRAME_SUPPORT', 'Your browser does not support iframes. Please upgrade or use a different browser') %>
		</iframe>
<% else %>
    $alertBody
<% end_if %>

<% if not $topOnly %>
	</div>
<% end_if %>
