;(function(window) {
if (BX.translit) return;

var
	arTransTable = null,
	translatorCache = [],
	defaultParams = {
		max_len: 100,
		change_case: 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
		replace_space: '_',
		replace_other: '_',
		delete_repeat_replace: true,
		use_google: false // BING is really using here but we won't rename a setting name now
	},
	r = {
		en: /[A-Z0-9]/i,
		space: /\s/
	};

BX.translit = function (str, params)
{
	if (null == params) params = {};
	for (var i in defaultParams)
	{
		if (typeof params[i] == 'undefined')
			params[i] = defaultParams[i];
	}
	if (params.change_case)
		params.change_case = params.change_case.toUpperCase();

	if (params.use_google && params.callback && !!BX.message('BING_KEY'))
	{
		return (new BX.CBingTranslator(str, params)).run();
	}
	else
	{
		var len = str.length;
		var str_new = '';
		var last_chr_new = '';

		for (var i = 0; i < len; i++)
		{
			var chr = str.charAt(i);

			if (r.en.test(chr))
			{
				chr_new = chr;
			}
			else if (r.space.test(chr))
			{
				if (
					!params.delete_repeat_replace
					||
					(i > 0 && last_chr_new != params.replace_space)
				)
					chr_new = params.replace_space;
				else
					chr_new = '';
			}
			else
			{
				var chr_new = __getChar(chr, params.change_case);

				if (null === chr_new)
				{
					if (
						!params.delete_repeat_replace
						||
						(i > 0 && i != len-1 && last_chr_new != params.replace_other)
					)
						chr_new = params.replace_other;
					else
						chr_new = '';
				}
			}

			if (null != chr_new && chr_new.length > 0)
			{
				switch(params.change_case)
				{
					case 'L': chr_new = chr_new.toLowerCase(); break;
					case 'U': chr_new = chr_new.toUpperCase(); break;
				}

				str_new += chr_new;
				last_chr_new = chr_new;
			}

			if (str_new.length >= params.max_len)
				break;
		}

		if (null != params.callback && BX.type.isFunction(params.callback))
		{
			params.callback(str_new)
			return str_new;
		}
		else
			return str_new;
	}
};

BX.CGoogleTranslator = function(str, params)
{
	this.str = str;
	this.params = params;
};

BX.CGoogleTranslator.prototype.run = function()
{
	var res = __checkCache(this.str);
	if (res)
		this.result({translation: res}, true);
	else
		this.translate();
};

BX.CGoogleTranslator.prototype.translate = function()
{
	if (!window.google || typeof(window.google.load) != "function")
	{
		if (BX.browser.IsIE())
		{
			var cb_ie = BX.proxy(this.translate, this);
			var cb = function() {
				setTimeout(function() {
					cb_ie(arguments);
				}, 100);
			}
		}
		else
		{
			var cb = BX.proxy(this.translate, this);
		}

		BX.loadScript('http://www.google.com/jsapi?rnd=' + Math.random(), cb);
	}
	else if (!window.google.language)
	{
		google.load(
			'language', 1, {callback: BX.proxy(this.translate, this)}
		);
	}
	else
	{
		google.language.translate(
			this.str,
			BX.message('LANGUAGE_ID'),
			"en",
			BX.delegate(this.result, this)
		);
	}
};

BX.CGoogleTranslator.prototype.result = function(result, bSkipCache)
{
	if (!bSkipCache)
		translatorCache[translatorCache.length] = {original: this.str, translation: result.translation};

	this.params.use_google = false;
	BX.translit(result.translation, this.params);
};

BX.CBingTranslator = function(str, params)
{
	BX.CBingTranslator.superclass.constructor.apply(this, arguments); 
};
BX.extend(BX.CBingTranslator, BX.CGoogleTranslator);

BX.CBingTranslator.prototype.translate = function()
{
	var cb_name = 'bing_translate_callback_' + parseInt(Math.random() * 10000),
		url = 'http://api.bing.net/json.aspx?AppId='+BX.message('BING_KEY')+'&Query=' + BX.util.urlencode(this.str.substr(0, 5000))+'&Sources=Translation&Version=' + (this.params.version||'2.2') + '&Translation.SourceLanguage='+BX.message('LANGUAGE_ID')+'&Translation.TargetLanguage=en&JsonType=callback&JsonCallback=' + cb_name;

	window[cb_name] = BX.proxy(this.result, this);
	BX.loadScript(url, function() {window[cb_name] = null});
};

BX.CBingTranslator.prototype.result = function(result, bSkipCache)
{
	var res = {translation: this.str};
	if (result)
	{
		if (result.translation)
			res = result;
		else if (
			result.SearchResponse 
			&& result.SearchResponse.Translation 
			&& result.SearchResponse.Translation.Results 
			&& result.SearchResponse.Translation.Results[0]
		)
		{
			res.translation = result.SearchResponse.Translation.Results[0].TranslatedTerm;
		}
	}

	return BX.CBingTranslator.superclass.result.apply(this, [res, bSkipCache]);
};

/* private static functions */

function __checkCache(str)
{
	for (var i = 0, len = translatorCache.length; i < len; i++)
	{
		if (translatorCache[i].original == str)
			return translatorCache[i].translation;
	}

	return null;
};

function __generateTransTable()
{
	var
		arFrom = (BX.message('TRANS_FROM') || '').split(','),
		arTo = (BX.message('TRANS_TO') || '').split(','),
		i, len;

	arTransTable = [];
	for (i = 0, len = arFrom.length; i < len; i++)
	{
		arTransTable[i] = [arFrom[i], arTo[i]];
	}
};

function __getChar(chr, change_case)
{
	if (null == arTransTable)
		__generateTransTable()

	for (var i=0, len = arTransTable.length; i < len; i++)
	{
		if (chr === arTransTable[i][0])
			return arTransTable[i][1];
	}

	return null;
};

})(window)