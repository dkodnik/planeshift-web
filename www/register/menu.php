<?
/*
 * menu.php - Author: John Sennesael
 *
 * Copyright (C) 2004 PlaneShift Team (info@planeshift.it,
 * http://www.planeshift.it)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation (version 2 of the License)
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Description : Navigational menu.
 *               
 */

  // simple security check
  if (!defined('psregister')) die ('You are not allowed to run this script directly.');

?>         
    <div id="navigation">
      <div class="downloadnow">
        <a href="http://www.planeshift.it/download.html">
          <img src="http://www.planeshift.it/graphics/buttons/download.gif" alt="Download" border="0" />
        </a>
      </div>

    <div class="expandcolapse">
      <a href="javascript:ddtreemenu.flatten('treemenu1', 'expand')">
        Expand All
      </a>
      |
      <a href="javascript:ddtreemenu.flatten('treemenu1', 'contact')">
        Collapse All
      </a>
    </div>

    <div class="menulinks">
      <ul id="treemenu1" class="treeview">
        <li>
          <img src="http://www.planeshift.it/graphics/closed.gif" alt="" border="0" />
          <a href="http://www.planeshift.it/about.html">
            <img src="http://www.planeshift.it/graphics/buttons/about.gif" border="0" alt="" />
          </a>
        </li>
        <li>
          <img src="http://www.planeshift.it/graphics/closed.gif" alt="" border="0" />
          <a href="http://www.planeshift.it/index.html">
            <img src="http://www.planeshift.it/graphics/buttons/news.gif" border="0" alt="" />
          </a>
        </li>
        <li>
          <img src="http://www.planeshift.it/graphics/closed.gif" alt="" border="0" />
          <img src="http://www.planeshift.it/graphics/buttons/account.gif" border="0" alt="" />
          <ul>
            <li><a href="http://www.planeshift.it/download.html">Download the game</a></li>
            <li><a href="http://www.planeshift.it/register.html">Register New</a></li>
            <li><a href="http://laanx.fragnetics.com/register/resendemail.php?forgot=yes">Password Recovery</a></li>
            <li><a href="http://laanx.fragnetics.com/">Server Page</a></li>
          </ul>
        </li>
        <li>
          <img src="http://www.planeshift.it/graphics/closed.gif" alt="" border="0" />
          <img src="http://www.planeshift.it/graphics/buttons/multimedia.gif" border="0" alt="" />
          <ul>
            <li><a href="http://www.planeshift.it/screenshots.html">Screenshots</a></li>
            <li><a href="http://www.planeshift.it/movies.html">Movies</a></li>
            <li><a href="http://www.planeshift.it/concept_art.html">Concept Art</a></li>
            <li><a href="http://www.planeshift.it/comics.html">Comics</a></li>
            <li><a href="http://www.planeshift.it/fan_art.html">Fan Art</a></li>
            <li><a href="http://www.planeshift.it/goodies.html">Goodies</a></li>
          </ul>
        </li>
        <li>
          <img src="http://www.planeshift.it/graphics/closed.gif" alt="" border="0" />
          <img src="http://www.planeshift.it/graphics/buttons/story.gif" border="0" alt="" />
          <ul>
            <li><a href="http://www.planeshift.it/setting.html">Setting Overview</a></li>
            <li><a href="http://www.planeshift.it/races.html">Races</a></li>
            <li><a href="http://www.planeshift.it/history.html">History</a></li>
            <li><a href="http://www.planeshift.it/government.html">Government</a></li>
            <li><a href="http://www.planeshift.it/economy.html">Economy</a></li>
          </ul>
        </li>
        <li>
          <img src="http://www.planeshift.it/graphics/closed.gif" alt="" border="0" />
          <img src="http://www.planeshift.it/graphics/buttons/documentation.gif" border="0" alt="" />
          <ul>
            <li><a href="http://www.planeshift.it/quickstart.html">Quick-Start Guide</a></li>
            <li><a href="http://www.planeshift.it/roleplay.html">Roleplay Guide</a></li>
            <li><a href="http://www.planeshift.it/faq.html">FAQ</a></li>
            <li><a href="http://www.planeshift.it/http://www.planeshift.it/guide/en/index.html">Player Guide</a></li>
          </ul>
        </li>
        <li>
          <img src="http://www.planeshift.it/graphics/closed.gif" alt="" border="0" />
          <img src="http://www.planeshift.it/graphics/buttons/support.gif" border="0" alt="" />
          <ul>
            <li><a href="http://www.planeshift.it/policies.html">Policies</a></li>
            <li><a href="http://www.planeshift.it/cheating.html">Player Cheating</a></li>
            <li><a href="http://www.planeshift.it/gamemasters.html">Game Masters</a></li>
            <li><a href="http://www.planeshift.it/bugreport.html">Reporting a Bug</a></li>
            <li><a href="http://www.planeshift.it/abuse.html">Reporting Abuse</a></li>
            <li><a href="http://www.planeshift.it/helpchannel.html">In-Game Help Channel</a></li>
          </ul>
        </li>
        <li>
          <img src="http://www.planeshift.it/graphics/closed.gif" alt="" border="0" />
          <img src="http://www.planeshift.it/graphics/buttons/community.gif" border="0" alt="" />
          <ul>
            <li><a href="http://www.planeshift.it/forums.html">Forums</a></li>
            <li><a href="http://www.planeshift.it/irc.html">IRC</a></li>
            <li><a href="http://www.planeshift.it/guilds.html">Guilds</a></li>
            <li><a href="http://www.planeshift.it/other_languages.html">Other Languages</a></li>
          </ul>
        </li>
        <li>
          <img src="http://www.planeshift.it/graphics/closed.gif" alt="" border="0" />
          <img src="http://www.planeshift.it/graphics/buttons/development.gif" border="0" alt="" />
          <ul>
            <li><a href="http://www.planeshift.it/team.html">The Team</a></li>
            <li><a href="http://www.planeshift.it/recruitment.html">Recruitment</a></li>
            <li><a href="http://www.planeshift.it/license.html">License</a></li>
            <li><a href="http://www.planeshift.it/sources.html">Source Code</a></li>
            <li><a href="http://www.planeshift.it/donate.html">Donate</a></li>
          </ul>
        </li>
      </ul>
     <script type="text/javascript">
       ddtreemenu.createTree("treemenu1", true)
     </script>
   </div>
 </div>

