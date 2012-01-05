SOLID PHP & Code Smells - Part 2: Open / Closed Principal
=========================================================
This is the source code used in my presentation on the Open / Closed Principal to the 
GTAPHP Meetup Group (http://meetup.gtaphp.org/events/45215312/).  You can see the 
slides and my notes on how I made the edits in this repository on my blog here:

http://blog.vicmetcalfe.com/2012/01/04/solid-php-code-smells-part-2-open-closed-principal/

Please post any comments, criticisms or suggestions there.  I look forward to your 
feedback!

The repository preserves the history of the code as I went through the presentation, 
so you can go back to the initial commit and view each set of changes as I refactored 
the code.

What is it?
-----------
The code we worked on follows the REST architecture for web services and provides 
services for maintaining a list of people called 'users'.  We start with that and then 
deal with a change request to handle book recommendations by users.  This triggers the 
bulk of the refactoring in the presentation.  Finally we get another change request to 
add support for XML encodings as well as JSON for another round of refactoring.

Please excuse some of the crappy things you'll find in this code, like minimal test 
code and poor handling of error conditions and edge cases.  I tried to keep it as 
simple as possible so we could get through as much as possible in an hour.

CodeBox
-------
I used a product called CodeBox which is a snippet manager for Mac to avoid wasting 
time typing blocks of code during the presentation.  The presenter's notes on my blog 
mention CodeBox shortcuts which begin ocp and which expand into the full snippet when 
typed.  These can be found in OCP.cbxml for those of you who have CodeBox and want to 
follow along.

A note about knockout.server.js
-------------------------------
This is my own knockout.js plugin which simplifies using REST services with knockout. 
It has a long way to go before I'd really be happy to release it.  For now it is 
awkward with master / detail relationships.  It pushes you to put all your models in a 
flat list like the sql tables behind them even when it would be better to roll the 
detail data into the master's model.  I've started work on a big update which 
addresses that problem, but you won't find that here.  Also I haven't written any 
documentation for it.  When I've addressed those two issues I'll publish it 
separately. Until then, use it at your own peril!
