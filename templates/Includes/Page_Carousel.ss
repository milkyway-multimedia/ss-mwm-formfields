<% if $outerCarouselNavigation && $carouselNavigation %>
    <% include Page_Carousel_Navigation %>
<% end_if %>

<% if $startCarousel %>
	<div <% if $carouselParentAttributes %>$carouselParentAttributes<% else %>id="$startCarousel"
	     class="carousel<% if $slideTransition %> $slideTransition<% else %> slide<% end_if %><% if $carouselClass %> $carouselClass<% end_if %>"
         data-ride="carousel" data-interval="false"<% end_if %>>
<% end_if %>

<% if not $outerCarouselNavigation && $carouselNavigation %>
    <% include Page_Carousel_Navigation %>
<% end_if %>

<% if $startCarousel %>
	<div class="carousel-inner">
<% end_if %>

<% if $slides %>
    <% loop $slides %>
        $FieldHolder
    <% end_loop %>
<% else %>
    <% if not $bottomOnly %>
        <div <% if $carouselAttributes %>$carouselAttributes<% else %>id="{$carouselName}-Slide" class="item carousel-body<% if $carouselActive %> active<% end_if %>"<% end_if %>>
    <% end_if %>

    <% if $carouselFields %>
        <% loop $carouselFields %>
            $FieldHolder
        <% end_loop %>
    <% else_if $carouselIframe %>
			<iframe src="$carouselIframe" class="modal-iframe" frameborder="0">
                <% _t('NO_IFRAME_SUPPORT', 'Your browser does not support iframes. Please upgrade or use a different browser') %>
			</iframe>
    <% else %>
        $carouselBody
    <% end_if %>

    <% if not $topOnly %>
		</div>
    <% end_if %>
<% end_if %>

<% if $endCarousel %>
	</div>
	</div>
<% end_if %>