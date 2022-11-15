# plg_content_qltabs

Just add start tags and one end tag into article text. The text inbetween the tags is going to be displayed in article a as tab.

~~~txt
{qltabs id="test"}{qltab title="One"}Yes here wa go. {qltab title="Two"}Wait for it! {qltab title="Three"}Well, done :-){/qltabs}
~~~

Settings in the {qltabs} tag will override default setting here in params. 

The text inbetween the {qltag} is going to be displayed in a tab.

You might also want to display with certain css. 
Just add the well-known style attribute as follows:

~~~txt
{qltabs style="background:red"} ...
~~~

The css class &quot;fadein&quot; rules the effect which is either &quot;fadein&quot;, &quot;slidedown&quot; or &quot;plop&quot;.

## What about a coffee ..

I love coding. My extensions are for free. Wanna say thanks? You're welcome! 
<https://www.buymeacoffee.com/mareikeRiegel>

## Varieties

~~~txt
# Standard displays as default setting chosen (horizontal, vertical oder accordeon)
{qltabs id="test1"}{qltab title="One"}Yes here wa go. {qltab title="Two"}Wait for it! {qltab title="Three"}Well, done :-){/qltabs}

# horizontal tabs
{qltabs id="test2" class="horizontal"}{qltab title="One"}Yes here wa go. {qltab title="Two"}Wait for it! {qltab title="Three"}Well, done :-){/qltabs}

# leftside menu / vertical tabs 
{qltabs id="test3-1" class="vertical"}{qltab title="One"}Yes here wa go. {qltab title="Two"}Wait for it! {qltab title="Three"}Well, done :-){/qltabs}
# leftside menu / vertical tabs width of 33% 
{qltabs id="test3-2 class="vertical qlwidth33"}{qltab title="One"}Yes here wa go. {qltab title="Two"}Wait for it! {qltab title="Three"}Well, done :-){/qltabs}

# accordeon menu
{qltabs id="test4" class="accordeon"}{qltab title="One"}Yes here wa go. {qltab title="Two"}Wait for it! {qltab title="Three"}Well, done :-){/qltabs}
~~~

## Line-break issue

As there are still some issues on line-breaks directly after a tag, please avoid linebreaks.

~~~txt
# works
{qltabs id="test"}{qltab title="One"}Yes here wa go.
asdasd
asdasd {qltab title="Two"}Wait for it!{/qltabs}

# does NOT work
{qltabs id="test"}{qltab title="One"}
Yes here wa go.
{qltab title="Two"}
Wait for it!
{/qltabs}
~~~
