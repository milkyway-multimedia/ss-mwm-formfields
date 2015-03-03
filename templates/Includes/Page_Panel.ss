<% if not $bottomOnly %>
    <div <% if $panelAttributes %>$panelAttributes<% else %>id="$panelName" class="panel<% if $panelClasses %>$panelClasses<% else %> panel-default<% end_if %>"<% end_if %>>
        <% if $panelTitle || $panelHeading %>
		<div class="panel-heading">
			<% if $panelHeading %>
				<h3 id="$panelName-Title" class="panel-title">$panelHeading</h3>
            <% else %>
                $panelTitle
			<% end_if %>
		</div>
        <% end_if %>
        <div class="panel-body">
<% end_if %>

<% if $panelFields %>
    <% loop $panelFields %>
        $FieldHolder
    <% end_loop %>
<% else_if $panelIframe %>
		<iframe src="$panelIframe" class="modal-iframe" frameborder="0">
            <% _t('NO_IFRAME_SUPPORT', 'Your browser does not support iframes. Please upgrade or use a different browser') %>
		</iframe>
<% else %>
    $panelBody
<% end_if %>

<% if not $topOnly %>
	</div>
	</div>
<% end_if %>
