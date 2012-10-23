function fixPNG(element)
{
  //���� ������� IE ������ 5.5-6
  if (/MSIE (5\.5|6).+Win/.test(navigator.userAgent))
  {
    var src;
    if (element.tagName==�IMG�) //���� ������� ������� �������� (��� IMG)
    {
      if (/\.png$/.test(element.src)) //���� ���� �������� ����� ���������� PNG
      {
        src = element.src;
        element.src = "/blank.gif"; //�������� ����������� ���������� gif-��
      }
    }
    else //�����, ���� ��� �� �������� � ������ �������
    {
   //���� � �������� ������ ������� ��������, �� ����������� �������� �������� background-�mage ���������� src
      src = element.currentStyle.backgroundImage.match(/url\("(.+\.png)"\)/i);
      if (src)
      {
        src = src[1]; //����� �� �������� �������� background-�mage ������ ����� ��������
        element.runtimeStyle.backgroundImage="none"; //������� ������� �����������
      }
    }
    //����, src �� ����, �� ����� ��������� ����������� � ������� ������� AlphaImageLoader
    if (src) element.runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=�" + src + "�,sizingMethod=�scale�)";
  }
}


