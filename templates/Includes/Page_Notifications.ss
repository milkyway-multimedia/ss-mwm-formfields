$onLoadAlert

<% if $hasSiteNotifications %>
    <% loop $siteNotifications %>
        $HTMLContent
    <% end_loop %>
<% end_if %>