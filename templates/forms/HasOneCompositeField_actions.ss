<% if $allowed('delete') || $allowed('detach') || $allowed('selector') %>
	<ul class="hasonecomposite-actions">
        <% if $allowed('selector') %>
            <li class="hasonecomposite-actions--item hasonecomposite-actions--item_selector">
                <a href="$Link('detach')"><% _t('HasOneCompositeField.DETACH', 'Detach') %></a>
            </li>
        <% end_if %>
        <% if $allowed('detach') %>
            <li class="hasonecomposite-actions--item hasonecomposite-actions--item_detach">
                <a href="$Link('detach')"><% _t('HasOneCompositeField.DETACH', 'Detach') %></a>
            </li>
        <% end_if %>
        <% if $allowed('delete') %>
            <li class="hasonecomposite-actions--item hasonecomposite-actions--item_delete">
                <a href="$Link('delete')"><% _t('HasOneCompositeField.DELETE', 'Delete') %></a>
            </li>
        <% end_if %>
    </ul>
<% end_if %>