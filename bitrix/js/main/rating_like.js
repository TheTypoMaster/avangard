if (!BXRL)
{
	var BXRL = {};
	var BXRLW = null;
}

RatingLike = function(likeId, entityTypeId, entityId, available, userId, localize, template, pathToUserProfile)
{	
	this.enabled = true;
	this.likeId = likeId;
	this.entityTypeId = entityTypeId;
	this.entityId = entityId;
	this.available = available == 'Y'? true: false;
	this.userId = userId;
	this.localize = localize;
	this.template = template;
	this.pathToUserProfile = pathToUserProfile;
	
	this.box = BX('bx-ilike-button-'+likeId);
	if (this.box === null)
	{
		this.enabled = false;
		return false;
	}
	
	this.button = BX.findChild(this.box, {className:'bx-ilike-left-wrap'}, true, false);
	this.buttonText = BX.findChild(this.button, {className:'bx-ilike-text'}, true, false);
	this.count = BX.findChild(this.box,  {tagName:'span', className:'bx-ilike-right-wrap'}, true, false);
	this.countText	= BX.findChild(this.count, {tagName:'span', className:'bx-ilike-right'}, true, false);
	this.popup = null;
	this.popupId = null;
	this.popupOpenId = null;
	this.popupTimeoutId = null;
	this.popupContent = BX.findChild(BX('bx-ilike-popup-cont-'+likeId), {tagName:'span', className:'bx-ilike-popup'}, true, false);
	this.popupContentPage = 1;	
	this.popupListProcess = false;	
	this.popupTimeout = false;	
	this.likeTimeout = false;	
	
	this.lastVote = BX.hasClass(template == 'standart'? this.button: this.count, 'bx-you-like')? 'plus': 'cancel';
}

RatingLike.Set = function(likeId, entityTypeId, entityId, available, userId, localize, template, pathToUserProfile)
{
	if (template === undefined)
		template = 'standart';
	BXRL[likeId] = new RatingLike(likeId, entityTypeId, entityId, available, userId, localize, template, pathToUserProfile);
	if (BXRL[likeId].enabled)
		RatingLike.Init(likeId);	
};

RatingLike.Init = function(likeId)
{
	// like/unlike button
	if (BXRL[likeId].available)
	{
		
		BX.bind(BXRL[likeId].template == 'standart'? BXRL[likeId].button: BXRL[likeId].buttonText, 'click' , function()
		{		
			clearTimeout(BXRL[likeId].likeTimeout);
			if (BX.hasClass(BXRL[likeId].template == 'standart'? this: BXRL[likeId].count, 'bx-you-like'))
			{
				BXRL[likeId].buttonText.innerHTML	=	BXRL[likeId].localize['LIKE_N'];
				BXRL[likeId].countText.innerHTML		= 	parseInt(BXRL[likeId].countText.innerHTML)-1;
				BX.removeClass(BXRL[likeId].template == 'standart'? this: BXRL[likeId].count, 'bx-you-like');
				
				BXRL[likeId].likeTimeout = setTimeout(function(){
					if (BXRL[likeId].lastVote != 'cancel')
						RatingLike.Vote(likeId, 'cancel');
				}, 1000);
			}
			else
			{
				BXRL[likeId].buttonText.innerHTML	=	BXRL[likeId].localize['LIKE_Y'];
				BXRL[likeId].countText.innerHTML 	= 	parseInt(BXRL[likeId].countText.innerHTML)+1;
				BX.addClass(BXRL[likeId].template == 'standart'? this: BXRL[likeId].count, 'bx-you-like');
				
				BXRL[likeId].likeTimeout = setTimeout(function(){
					if (BXRL[likeId].lastVote != 'plus')
						RatingLike.Vote(likeId, 'plus');
				}, 1000);
			}
			BX.removeClass(this.box, 'bx-ilike-button-hover');
		});
		// Hover/unHover like-button
		BX.bind(BXRL[likeId].box, 'mouseover', function() {BX.addClass(this, 'bx-ilike-button-hover')});
		BX.bind(BXRL[likeId].box, 'mouseout', function() {BX.removeClass(this, 'bx-ilike-button-hover')});
	}
	else
	{
		if (BXRL[likeId].buttonText != undefined)
			BXRL[likeId].buttonText.innerHTML	=	BXRL[likeId].localize['LIKE_D'];
	}
	// get like-user-list
	RatingLike.PopupScroll(likeId);
	
	BX.bind(BXRL[likeId].count, 'mouseover' , function() {
		clearTimeout(BXRL[likeId].popupTimeoutId);
		BXRL[likeId].popupTimeoutId = setTimeout(function(){
			if (BXRLW == likeId)
				return false;
			if (BXRL[likeId].popupContentPage == 1)
				RatingLike.List(likeId, 1);
			BXRL[likeId].popupTimeoutId = setTimeout(function() {
				RatingLike.OpenWindow(likeId);
			}, 400);
		}, 400);
	});
	BX.bind(BXRL[likeId].count, 'mouseout' , function() {
		clearTimeout(BXRL[likeId].popupTimeoutId);
	});	
	BX.bind(BXRL[likeId].count, 'click' , function() {
		clearTimeout(BXRL[likeId].popupTimeoutId);	
		if (BXRL[likeId].popupContentPage == 1)
			RatingLike.List(likeId, 1);
		RatingLike.OpenWindow(likeId);
	});
	
	BX.bind(BXRL[likeId].box, 'mouseout' , function() {
		clearTimeout(BXRL[likeId].popupTimeout);
		BXRL[likeId].popupTimeout = setTimeout(function(){
			if (BXRL[likeId].popup !== null)
			{
				BXRL[likeId].popup.close();
				BXRLW = null;
			}
		}, 1000);
	});
	BX.bind(BXRL[likeId].box, 'mouseover' , function() {
		clearTimeout(BXRL[likeId].popupTimeout);
	});	
}

RatingLike.OpenWindow = function(likeId)
{
	if (parseInt(BXRL[likeId].countText.innerHTML) == 0)
		return false;
	
	if (BXRL[likeId].popup == null)	
	{
		BXRL[likeId].popup = new BX.PopupWindow('ilike-popup-'+likeId, (BXRL[likeId].template == 'standart'? BXRL[likeId].count: BXRL[likeId].box), { 	content:BX('bx-ilike-popup-cont-'+likeId), lightShadow:true, autoHide:true, zIndex: 2500 });			
		BX.bind(BX('ilike-popup-'+likeId), 'mouseout' , function() {
			clearTimeout(BXRL[likeId].popupTimeout);
			BXRL[likeId].popupTimeout = setTimeout(function(){
				BXRL[likeId].popup.close();
				BXRLW = null;
			}, 1000);		
		});
		
		BX.bind(BX('ilike-popup-'+likeId), 'mouseover' , function() {
			clearTimeout(BXRL[likeId].popupTimeout);
		});
	}
	else if(BX('ilike-popup-'+likeId).style.display == "block")
	{
		BXRL[likeId].popup.close();
		BXRLW = null;
		return false
	}
	
	if (BXRLW != null)
		BXRL[BXRLW].popup.close();
	
	BXRL[likeId].popup.show();
	BXRLW = likeId;
	
	if (BXRL[likeId].template == 'standart')
		BXRL[likeId].popup.setAngle({
			position:'bottom',
			offset:function(){
			  return (((BXRL[likeId].popup.popupContainer.offsetWidth - BXRL[likeId].count.offsetWidth)) - 8);
			}()
		});
	else
		BXRL[likeId].popup.setAngle({
			position:'bottom'
		});
		
	RatingLike.AdjustWindow(likeId);
}

RatingLike.Vote = function(likeId, voteAction)
{
	BX.ajax({
		url: '/bitrix/components/bitrix/rating.vote/vote.ajax.php',
		method: 'POST',
		dataType: 'json',
		data: {'RATING_VOTE' : 'Y', 'RATING_VOTE_TYPE_ID' : BXRL[likeId].entityTypeId, 'RATING_VOTE_ENTITY_ID' : BXRL[likeId].entityId, 'RATING_VOTE_ACTION' : voteAction, 'sessid': BX.bitrix_sessid()},
		onsuccess: function(data)	{
			BXRL[likeId].lastVote = data.action;
			BXRL[likeId].countText.innerHTML = data.items_all;
			BXRL[likeId].popupContentPage = 1;
			
			BXRL[likeId].popupContent.innerHTML = '';	
			spanTag0 = document.createElement("span"); 
			spanTag0.className = "bx-ilike-wait";
			BXRL[likeId].popupContent.appendChild(spanTag0);
			RatingLike.AdjustWindow(likeId);
			
			if(BX('ilike-popup-'+likeId).style.display == "block")
				RatingLike.List(likeId, null);
		},
		onfailure: function(data)	{} 
	});
	return false;
}

RatingLike.List = function(likeId, page)
{
	if (parseInt(BXRL[likeId].countText.innerHTML) == 0)
		return false;
	
	if (page == null)
		page = BXRL[likeId].popupContentPage;
	
	BXRL[likeId].popupListProcess = true;
	BX.ajax({
		url: '/bitrix/components/bitrix/rating.vote/vote.ajax.php',
		method: 'POST',
		dataType: 'json',
		data: {'RATING_VOTE_LIST' : 'Y', 'RATING_VOTE_TYPE_ID' : BXRL[likeId].entityTypeId, 'RATING_VOTE_ENTITY_ID' : BXRL[likeId].entityId, 'RATING_VOTE_LIST_PAGE' : page, 'PATH_TO_USER_PROFILE' : BXRL[likeId].pathToUserProfile, 'sessid': BX.bitrix_sessid()},
		onsuccess: function(data)
		{
			BXRL[likeId].countText.innerHTML = data.items_all;	
			
			if ( parseInt(data.items_page) == 0 )
				return false;
								
			if (page == 1)
			{
				BXRL[likeId].popupContent.innerHTML = '';
				spanTag0 = document.createElement("span"); 
				spanTag0.className = "bx-ilike-bottom_scroll";
				BXRL[likeId].popupContent.appendChild(spanTag0);
			}
			BXRL[likeId].popupContentPage += 1;

			for (var i in data.items) {					
				aTag = document.createElement("a"); 
				aTag.className = "bx-ilike-popup-img";
				aTag.href = data.items[i]['URL'];
				aTag.target = "_blank";
					
					spanTag1 = document.createElement("span"); 
					spanTag1.className = "bx-ilike-popup-avatar";
					spanTag1.innerHTML = data.items[i]['PHOTO'];
					aTag.appendChild(spanTag1);
					
					spanTag2 = document.createElement("span"); 
					spanTag2.className = "bx-ilike-popup-name";
					spanTag2.appendChild(document.createTextNode(BX.util.htmlspecialcharsback(data.items[i]['FULL_NAME'])));
					aTag.appendChild(spanTag2);
					
				BXRL[likeId].popupContent.appendChild(aTag);	
			}

			RatingLike.AdjustWindow(likeId);
			RatingLike.PopupScroll(likeId);
			
			BXRL[likeId].popupListProcess = false;
		},	
		onfailure: function(data)	{} 
	});
	return false;
}

RatingLike.AdjustWindow = function(likeId)
{
	children = BX.findChild(BXRL[likeId].popupContent, {className:'bx-ilike-popup-img'}, true, true);
	if (children !== null)
	{
		iOffsetHeight = BX.browser.IsIE()? 5: 0;
		for (var i in children) {	
			iOffsetHeight += children[i].offsetHeight;
		}
	}
	else 
		iOffsetHeight = BX.browser.IsIE()? 35: 30;

	if (iOffsetHeight < 121)
		BXRL[likeId].popupContent.style.height = iOffsetHeight+'px';
	else
		BXRL[likeId].popupContent.style.height = '121px';


	var offsetTop = 5;
	if (BXRL[likeId].template == 'light')
		offsetTop = 0;
		
	arScroll = BX.GetWindowScrollPos();
	iLeft = (BXRL[likeId].template == 'standart'? BXRL[likeId].popup.bindElementPos.left-BXRL[likeId].popup.popupContainer.offsetWidth+BXRL[likeId].count.offsetWidth+8: (BXRL[likeId].popup.bindElementPos.left-7));
	iLeftAngle = (BXRL[likeId].template == 'standart'? (((BXRL[likeId].popup.popupContainer.offsetWidth - BXRL[likeId].box.offsetWidth) / 2 + BXRL[likeId].box.offsetWidth) - BX.findChild(BXRL[likeId].box, {className:'bx-ilike-right-wrap'}, true, false).offsetWidth / 2) - 8: 0);
	iWindow = iLeft+BXRL[likeId].popup.popupContainer.offsetWidth;
	iBody = document.body.clientWidth + arScroll.scrollLeft;
	
	if (iWindow>iBody)
	{
		iLeft = iLeft-(iWindow-iBody);
		BXRL[likeId].popup.setAngle({ offset : (iWindow-iBody)+iLeftAngle });
	} 
	else if (iLeft<0)
	{
		BXRL[likeId].popup.setAngle({ offset : (iLeft)+iLeftAngle });
		iLeft = 0;
	}
	
	BX.adjust(BX('ilike-popup-'+likeId), {	style: {	
		top: BXRL[likeId].popup.bindElementPos.top-(BXRL[likeId].popup.popupContainer.offsetHeight+offsetTop) + "px",
		left: iLeft+"px"
	}});
}

RatingLike.PopupScroll = function(likeId)
{
	BX.bind(BXRL[likeId].popupContent, 'scroll' , function() {
		if (this.scrollTop > (this.scrollHeight - this.offsetHeight) / 1.5)
		{
			RatingLike.List(likeId, null);
			BX.unbindAll(this);
		}
	});
}