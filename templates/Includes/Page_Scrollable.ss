<% if $scrollIframe %>
		<iframe src="$scrollIframe" class="scrollable-iframe" frameborder="0">
            <% _t('NO_IFRAME_SUPPORT', 'Your browser does not support iframes. Please upgrade or use a different browser') %>
		</iframe>
<% else %>
    <% if not $bottomOnly %>
		<div id="$scrollName-Scrollable" class="scrollable<% if $scrollClass %> $scrollClass<% end_if %>">
    <% end_if %>

    $scrollBody

    <% if not $topOnly %>
	    </div>
    <% end_if %>
<% end_if %>
