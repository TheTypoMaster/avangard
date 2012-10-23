function fixPNG(element)
{
  //≈сли браузер IE версии 5.5-6
  if (/MSIE (5\.5|6).+Win/.test(navigator.userAgent))
  {
    var src;
    if (element.tagName==ТIMGТ) //≈сли текущий элемент картинка (тэг IMG)
    {
      if (/\.png$/.test(element.src)) //≈сли файл картинки имеет расширение PNG
      {
        src = element.src;
        element.src = "/blank.gif"; //замен€ем изображение прозрачным gif-ом
      }
    }
    else //иначе, если это не картинка а другой элемент
    {
   //если у элемента задана фонова€ картинка, то присваеваем значение свойства background-шmage переменной src
      src = element.currentStyle.backgroundImage.match(/url\("(.+\.png)"\)/i);
      if (src)
      {
        src = src[1]; //берем из значени€ свойства background-шmage только адрес картинки
        element.runtimeStyle.backgroundImage="none"; //убираем фоновое изображение
      }
    }
    //если, src не пуст, то нужно загрузить изображение с помощью фильтра AlphaImageLoader
    if (src) element.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=Т" + src + "С,sizingMethod=ТscaleТ)";
  }
}


