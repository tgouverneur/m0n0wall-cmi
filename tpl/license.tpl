<?php
 /**
  * Base file for HTML processing
  *
  * @author Gouverneur Thomas <tgouverneur@be.tiauto.com>
  * @copyright Copyright (c) 2007-2008, Gouverneur Thomas - TI Automotive
  * @version 1.0
  * @package html
  * @subpackage html
  * @category html
  * @filesource
  */
?>
<p class="pgtitle"><?php echo $pagename; ?></p>

<table width="70%" border="0" cellpadding=0" cellspacing="0">
 <tr>
  <td>
   <h2>Greetings</h2>
   <p>
    I would like to thank <a href="http://www.tiautomotive.com">TI Automotive</a> for giving me
   the time to start developping m0n0wall-CMI, for the basic idea and for the support they have given.
   </p>
   <p>Especially, I'd like to thank <i>Benjamin Constant</i>, <i>Laurent Grilli</i> and <i>Jacques Renier</i>,
    part of the <i>TI Automotive</i> firm for their help and support.
   <br/>
   <p>
   Finally, thanks to all the testers and bug-reporters of m0n0wall-CMI.
   </p>
  </td>
 </tr>
 <tr>
  <td>
<h2>License</h2>
<p>The logo of m0n0wall is the property of Manuel Kasper &lt;mk@neon1.net&gt;,<br/>
Some part of this web interface are also ripped from his work.<br/>
All other part of this Central Management Interface are under the BSD license.
</p>

<p>All of the documentation and software included in m0n0wall-CMI is copyrighted by Gouverneur Thomas.</p>
<center><p>Copyright 2007, 2008 Gouverneur Thomas. All rights reserved.</p></center>

<p>Redistribution and use in source and binary forms, with or without modification, are
permitted provided that the following conditions are met:</p>

<ol>
<li>Redistributions of source code must retain the above copyright notice, this list of
conditions and the following disclaimer.</li>

<li>Redistributions in binary form must reproduce the above copyright notice, this list
of conditions and the following disclaimer in the documentation and/or other materials
provided with the distribution.</li>

<li>All advertising materials mentioning features or use of this software must display
the following acknowledgement:

<blockquote>This product includes software developed by Gouverneur Thomas,
and its contributors.</blockquote>
</li>

<li>Neither the name of the University nor the names of its contributors may be used to
endorse or promote products derived from this software without specific prior written
permission.</li>
</ol>

<p>THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL
THE REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.</p>

<p>The views and conclusions contained in the software and documentation are those of the
authors and should not be interpreted as representing official policies, either expressed
or implied, of Gouverneur Thomas. </p>

  </td>
 </tr>
</table>

<br/><br/>

<?php if (isset($link)): ?>
<a href="<?php echo $link["href"]; ?>"><?php echo $link["label"]; ?></a><br/>
<?php endif; ?>
