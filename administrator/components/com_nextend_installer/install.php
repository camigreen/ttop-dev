<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
jimport('joomla.installer.helper');
jimport('joomla.filesystem.folder');

if (!function_exists('deleteExtFolder')) {

    function deleteExtFolder() {
        $pkg_path = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_nextend_installer' . DIRECTORY_SEPARATOR . 'extensions';
        if (file_exists($pkg_path)) JFolder::delete($pkg_path);
        if (!version_compare(JVERSION, '1.6.0', 'lt')) {
            $db = JFactory::getDBO();
            $db->setQuery("DELETE FROM #__menu WHERE title='com_nextend_installer'");
            $db->query();
        }
    }

    function com_install() {
        register_shutdown_function("deleteExtFolder");
        $installer = new Installer();
        $installer->install();
        return true;
    }

    function com_uninstall() {
        $installer = new Installer();
        $installer->uninstall();
        return true;
    }

    class Installer extends JObject {

        var $name = 'Nextend Installer';
        var $com = 'com_nextend_installer';

        function install() {
            $pkg_path = str_replace('/',DIRECTORY_SEPARATOR,JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $this->com . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR);

            if (JFolder::exists($pkg_path . 'nextend')) {
                $librariesPath = defined('JPATH_LIBRARIES') ? JPATH_LIBRARIES : JPATH_PLATFORM;
                JFolder::copy($pkg_path . 'nextend', $librariesPath . DIRECTORY_SEPARATOR . 'nextend', '', true);
                JFolder::delete($pkg_path . 'nextend');
            }


            $extensions = array_merge(JFolder::folders($pkg_path, '^(?!com_)\w+$'), JFolder::folders($pkg_path, '^com_\w+$'));
            if (($key = array_search('plugins', $extensions)) !== false) {
                unset($extensions[$key]);
                foreach (JFolder::folders($pkg_path . 'plugins'.DIRECTORY_SEPARATOR, '.', false, true) AS $f) {
                    $plgs = JFolder::folders($f, '.', false, true);
                    for ($i = 0; $i < count($plgs); $i++) {
                        $plgs[$i] = str_replace($pkg_path, '', $plgs[$i]);
                    }
                    $extensions = array_merge($extensions, $plgs);
                }
            }


            if (version_compare(JVERSION,'1.6.0','l')) {
              foreach($extensions as $pkg) {
                $f = $pkg_path.DIRECTORY_SEPARATOR.$pkg;
                $xmlfiles = JFolder::files($f, '.xml$', 1, true);
                foreach($xmlfiles AS $xmlf){
                  $file = file_get_contents($xmlf);
                  JFile::write($xmlf, preg_replace("/<\/extension/","</install",preg_replace("/<extension/","<install",$file)));
                }
              }
            }

            foreach ($extensions as $pkg) {
                $installer = new JInstaller();
                $installer->setOverwrite(true);
                if ($success = $installer->install($pkg_path . DIRECTORY_SEPARATOR . $pkg)) {
                } else {
                    $msgcolor = "#FFD0D0";
                    $msgtext = "ERROR: Could not install the $pkg. Please contact us on our support page: http://www.nextendweb.com/help/support";
                    ?>
                    <table bgcolor="<?php echo $msgcolor; ?>" width="100%">
                        <tr style="height:30px">
                            <td><font size="2"><b><?php echo $msgtext; ?></b></font></td>
                        </tr>
                    </table>
                <?php
                }
                if ($success && file_exists($pkg_path . "/" . $pkg . "/install.php")) {
                    require_once $pkg_path . "/" . $pkg . "/install.php";
                    $com = new $pkg();
                    $com->install();
                }
                if ($success && file_exists($pkg_path . "/" . $pkg . "/message.php")) {
                    include($pkg_path . "/" . $pkg . "/message.php");
                }
            }
            $db = JFactory::getDBO();
            if (version_compare(JVERSION, '1.6.0', 'lt')) {
                $db->setQuery("UPDATE #__plugins SET published=1 WHERE name LIKE '%nextend%'");
            } else {
                $db->setQuery("UPDATE #__extensions SET enabled=1 WHERE name LIKE '%nextend%' AND type='plugin'");
            }
            $db->query();

            if (JFolder::exists(JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'dojo' . DIRECTORY_SEPARATOR)) {
                JFolder::delete(JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'dojo' . DIRECTORY_SEPARATOR);
                JFolder::create(JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'dojo' . DIRECTORY_SEPARATOR);
            }
        }

        function uninstall() {
        }

    }

    class com_nextend_installerInstallerScript {
        function install($parent) {
            com_install();
        }

        function uninstall($parent) {
            com_uninstall();
        }

        function update($parent) {
            com_install();
        }
    }
}