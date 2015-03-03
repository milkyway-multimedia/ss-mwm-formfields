<% if $tabNavigation %>
    <% include Page_Tabs_Navigation %>
<% end_if %>

<% if $startTabs %>
	<div <% if $tabParentAttributes %>$tabParentAttributes<% else %>id="$startTabs" class="tab-content"<% end_if %>>
<% end_if %>

<% if $tabs %>
    <% loop $tabs %>
        $FieldHolder
    <% end_loop %>
<% else %>
    <% if not $bottomOnly %>
		<div <% if $tabAttributes %>$tabAttributes<% else %>id="$tabName"
				class="tab-pane fade<% if $tabActive %> in active<% end_if %>"<% end_if %>>
    <% end_if %>

    <% if $tabFields %>
        <% loop $tabFields %>
            $FieldHolder
        <% end_loop %>
    <% else_if $tabIframe %>
			<iframe src="$tabIframe" class="modal-iframe" frameborder="0">
                <% _t('NO_IFRAME_SUPPORT', 'Your browser does not support iframes. Please upgrade or use a different browser') %>
			</iframe>
    <% else %>
        $tabBody
    <% end_if %>

    <% if not $topOnly %>
		</div>
    <% end_if %>
<% end_if %>

<% if $endTabs %>
	</div>
<% end_if %>