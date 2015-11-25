<div class="tabbedselectiongroup<% if not $IsReadonly %> tabbedselectiongroup-selectable<% end_if %><% if $IsVertical %> tabbedselectiongroup_vertical<% end_if %><% if $extraClass %> $extraClass<% end_if %>">
    <% if $IsReadonly %>
    <ul class="tabbedselectiongroup-tabs<% if $extraClass %> $extraClass<% end_if %>">
        <% loop $FieldList %>
            <% if $Selected %>
                <li$Selected>
                    $RadioLabel
                    $FieldHolder
                </li>
            </ul>
            <% end_if %>
        <% end_loop %>
    <% else %>
        <ul class="tabbedselectiongroup-tabs">
            <% if $LabelTab %>
                <li class="tabbedselectiongroup-anchor-holder tabbedselectiongroup-anchor-holder_label">
                    <label class="tabbedselectiongroup-tabs-label">$LabelTab</label>
                </li>
            <% end_if %>
            <% if $ShowTabsAsDropdown %>

                <li class="tabbedselectiongroup-anchor-holder tabbedselectiongroup-options-holder active">
                    <label class="tabbedselectiongroup-anchor" data-open-dropdown="#{$ID}-Dropdown">
                        <span class="tabbedselectiongroup-anchor--title tabbedselectiongroup-options-selected--title">
                            <% if $InitiallySelected %>
                                $InitiallySelected.Title
                            <% else %>
                                <% _t('(none)', '(none)') %>
                            <% end_if %>
                        </span>
                        <i class="tabbedselectiongroup-anchor-caret fa fa-caret-down"></i>
                    </label>

                    <ul class="tabbedselectiongroup-options" id="{$ID}-Dropdown">
                        <% loop $FieldList %>
                            <li class="tabbedselectiongroup-option--anchor-holder<% if $Selected %> active<% end_if %>">
                                <label class="tabbedselectiongroup-option-anchor" for="$ID" data-open="tab"
                                       data-target="#{$ID}-tabContent">
                                    $RadioButton
                                    <span class="tabbedselectiongroup-option-anchor--title">$Title</span>
                                </label>
                            </li>
                        <% end_loop %>
                    </ul>
                </li>

            <% else %>
                <% loop $FieldList %>
                    <li class="tabbedselectiongroup-anchor-holder<% if $Selected %> active<% end_if %>">
                        <label class="tabbedselectiongroup-anchor" for="$ID" data-open="tab"
                               data-target="#{$ID}-tabContent">
                            $RadioButton
                            <span class="tabbedselectiongroup-anchor--title">$Title</span>
                        </label>
                    </li>
                <% end_loop %>
            <% end_if %>
        </ul>

        <div class="tabbedselectiongroup-tabs--panes">
            <% loop $FieldList %>
                <div id="{$ID}-tabContent" class="tabbedselectiongroup-tabs--pane<% if $Selected %> active<% end_if %>">
                    $FieldHolder
                </div>
            <% end_loop %>
        </div>
    <% end_if %>
</div>
