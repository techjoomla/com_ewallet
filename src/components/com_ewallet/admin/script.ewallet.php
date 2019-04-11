<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.controller');
	if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
	}
class com_ewalletInstallerScript
{	
	/** @var array The list of extra modules and plugins to install */	
	private $oldversion="";
	private $componentStatus="install";   // used to identify new install or update
	private $installation_queue = array(
		// modules => { (folder) => { (module) => { (position), (published) } }* }*
		'modules'=>array(
			'admin'=>array(
						
						),
			'site'=>array(
					'mod_eWalletBlance' => array('position-7', 1)
					
						)
		),
		// plugins => { (folder) => { (element) => (published) }* }*
		'plugins'=>array(
			'payment'=>array(
				'ewallet'=>1,
				'2checkout'=>0,
				'alphauserpoints'=>0,
				'authorizenet'=>0,
				'bycheck'=>1,
				'byorder'=>1,
				'ccavenue'=>0,
				'jomsocialpoints'=>0,
				'linkpoint'=>0,
				'paypal'=>0,
				'paypalpro'=>0,
				'payu'=>0
			)
		)
	);
	
	private $uninstall_queue = array(
		// modules => { (folder) => { (module) => { (position), (published) } }* }*
		'modules'=>array(
			'admin'=>array(
						
						),
			'site'=>array(
						)
		),
		// plugins => { (folder) => { (element) => (published) }* }*
		'plugins'=>array(
			
		)
	);
	
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		// Only allow to install on Joomla! 2.5.0 or later
		//return version_compare(JVERSION, '2.5.0', 'ge');		
	}

	/**
	 * Runs after install, update or discover_update
	 * @param string $type install, update or discover_update
	 * @param JInstaller $parent
	 */
	function postflight( $type, $parent )
	{
		$msgBox=array();
		// Install subextensions
		$status = $this->_installSubextensions($parent);
		// Install FOF
		$fofStatus = $this->_installFOF($parent);

		// Install Techjoomla Straper
		$straperStatus = $this->_installStraper($parent);
		
		$newpath =JPATH_ROOT.DS.'images'.DS.'ewallet';
		if(!JFolder::exists($newpath))
		{
			JFolder::create($newpath);
			JFile::move(JPATH_ROOT.DS.'components'.DS.'com_ewallet'.DS.'assets'.DS.'images'.DS.'default_currency.png', $newpath.DS.'default_currency.png');
		}
		
		//$fofStatus=$straperStatus='';
		if(version_compare(JVERSION, '3.0', 'lt')) {
			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root().'/media/techjoomla_strapper/css/bootstrap.min.css' );
		}
		// Do all releated Tag line/ logo etc
		$this->taglinMsg();
		// Show the post-installation page
		$this->_renderPostInstallation($status, $fofStatus, $straperStatus, $parent,$msgBox);
	}
		/**
	 * Renders the post-installation message
	 */
	private function _renderPostInstallation($status, $fofStatus, $straperStatus, $parent,$msgBox=array())
	{
		if(version_compare(JVERSION, '3.0', 'lt')) {	
			 $document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root().'/media/techjoomla_strapper/css/bootstrap.min.css' );	
		}
		

		$enable="<span class=\"label label-success\">Enabled</span>";
		$disable= "<span class=\"label label-important\">Disabled</span>";
		$updatemsg="Updated Successfully";	 
			
		?>
		<?php $rows = 1;?>
		<div class="techjoomla-bootstrap" >
		<table class="table-condensed table">
			<thead>
				<tr class="row1">
					<th class="title" colspan="2">Extension</th>
					<th width="30%">Status</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row2">
					<td class="key" colspan="2"><strong>Ewallet component</strong></td>
					<td><strong style="color: green">Installed</strong></td>
				</tr>
					<tr class="row2">
					<td class="key" colspan="2">
						<strong>Framework on Framework (FOF) <?php echo $fofStatus['version']?></strong> [<?php echo $fofStatus['date'] ?>]
					</td>
					<td><strong>
						<span style="color: <?php echo $fofStatus['required'] ? ($fofStatus['installed']?'green':'red') : '#660' ?>; font-weight: bold;">
							<?php echo $fofStatus['required'] ? ($fofStatus['installed'] ?'Installed':'Not Installed') : 'Already up-to-date'; ?>
						</span>
					</strong></td>
				</tr>
				<tr class="row2">
					<td class="key" colspan="2">
						<strong>TechJoomla Strapper <?php echo $straperStatus['version']?></strong> [<?php echo $straperStatus['date'] ?>]
					</td>
					<td><strong>
						<span style="color: <?php echo $straperStatus['required'] ? ($straperStatus['installed']?'green':'red') : '#660' ?>; font-weight: bold;">
							<?php echo $straperStatus['required'] ? ($straperStatus['installed'] ?'Installed':'Not Installed') : 'Already up-to-date'; ?>
						</span>
					</strong></td>
				</tr>
				<?php
					if(!empty($msgBox))
					{
						// strore releated msg and menu releated msg
							foreach($msgBox as $key=>$msgTopic)
							{
								
								if(!empty($msgTopic))
								{
									foreach($msgTopic as $indexMsg=>$statusMsg)
									{
										if(!empty($statusMsg))
										{
									?>
									<tr class="row2"> 
										<td class="key" colspan="2"><strong><?php echo $indexMsg;?></strong></td>
										<td><strong style="color: <?php echo ($statusMsg)? "green" : "red"?>"><?php echo ($statusMsg)? $statusMsg:''; ?></strong>
									</tr>
									<?php	
										}
									}
									
								}
							}
					}
				?>
				
				<?php if (count($status->modules)) : ?>
				<tr class="row1">
					<th>Module</th>
					<th>Client</th>
					<th></th>
					</tr>
				<?php foreach ($status->modules as $module) : ?>
				<tr class="row2 <?php //echo ($rows++ % 2); ?>">
					<td class="key"><?php echo ucfirst($module['name']); ?></td>
					<td class="key"><?php echo ucfirst($module['client']); ?></td>
					<td><strong style="color: <?php echo ($module['result'])? "green" : "red"?>"><?php echo ($this->componentStatus=="install") ?(($module['result'])?'Installed':'Not installed'):$updatemsg; ?></strong>
					
					<?php
					if($this->componentStatus=="install")
					{
						if(!empty($module['result'])) // if installed then only show msg
						{	
							echo $mstat=($module['status']? $enable :$disable);
						}
					}
					?>				
					</td>
				</tr>
				<?php endforeach;?>
				<?php endif;?>
				<?php if (count($status->plugins)) : ?>
				<tr class="row1">
					<th colspan="2">Plugin</th>
			<!--		<th>Group</th> -->
					<th></th>
				</tr>
				<?php 
					$oldplugingroup="";
				foreach ($status->plugins as $plugin) :
					if($oldplugingroup!=$plugin['group'])
					{
						$oldplugingroup=$plugin['group'];
				?>
					<tr class="row0">
						<th colspan="2"><strong><?php echo ucfirst($oldplugingroup)." Plugins";?></strong></th>
						<th></th>
				<!--		<td></td> -->
					</tr>
				<?php
					}
				
				 ?>
				<tr class="row2 <?php //echo ($rows++ % 2); ?>">
					<td colspan="2" class="key"><?php echo ucfirst($plugin['name']); ?></td>
		<!--			<td class="key"><?php //echo ucfirst($plugin['group']); ?></td> -->
					<td><strong style="color: <?php echo ($plugin['result'])? "green" : "red"?>"><?php echo ($this->componentStatus=="install") ?(($plugin['result'])?'Installed':'Not installed'):$updatemsg; ?></strong>
					<?php
					if($this->componentStatus=="install")
					{
						if(!empty($plugin['result']))
						{								
						echo $pstat=($plugin['status']? "<span class=\"label label-success\">Enabled</span>" : "<span class=\"label label-important\">Disabled</span>");
								
						}
					}
					?>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		</div> <!-- end akeeba bootstrap --> 

		<?php
		
	}

	/**
	 * Installs subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param JInstaller $parent
	 * @return JObject The subextension installation status
	 */
	private function _installSubextensions($parent)
	{
		$src = $parent->getParent()->getPath('source');

		$db = JFactory::getDbo();

		$status = new JObject();
		$status->modules = array();
		$status->plugins = array();

		// Modules installation
		
		if(count($this->installation_queue['modules'])) {
			foreach($this->installation_queue['modules'] as $folder => $modules) {
				if(count($modules)) 
					foreach($modules as $module => $modulePreferences)
					{
						// Install the module
						if(empty($folder)) 
							$folder = 'site';
						$path = "$src/modules/$folder/$module";
						if(!is_dir($path))// if not dir
						{
							$path = "$src/modules/$folder/mod_$module";
						}
						if(!is_dir($path)) {
							$path = "$src/modules/$module";
						}
					
						if(!is_dir($path)) {
							$path = "$src/modules/mod_$module";
						}
						if(!is_dir($path)) 
						{
								
							$fortest='';
							//continue;
						}
							
						// Was the module already installed?
						$sql = $db->getQuery(true)
							->select('COUNT(*)')
							->from('#__modules')
							->where($db->qn('module').' = '.$db->q('mod_'.$module));
						$db->setQuery($sql);
						
						$count = $db->loadResult();
						$installer = new JInstaller;
						$result = $installer->install($path);
						$status->modules[] = array(
							'name'=>$module,
							'client'=>$folder,
							'result'=>$result,
							'status'=>$modulePreferences[1]
						);
						// Modify where it's published and its published state
						if(!$count) {
							// A. Position and state
							list($modulePosition, $modulePublished) = $modulePreferences;
							if($modulePosition == 'cpanel') {
								$modulePosition = 'icon';
							}
							$sql = $db->getQuery(true)
								->update($db->qn('#__modules'))
								->set($db->qn('position').' = '.$db->q($modulePosition))
								->where($db->qn('module').' = '.$db->q('mod_'.$module));
							if($modulePublished) {
								$sql->set($db->qn('published').' = '.$db->q('1'));
							}
							$db->setQuery($sql);
							$db->query();

							// B. Change the ordering of back-end modules to 1 + max ordering
							if($folder == 'admin') {
								$query = $db->getQuery(true);
								$query->select('MAX('.$db->qn('ordering').')')
									->from($db->qn('#__modules'))
									->where($db->qn('position').'='.$db->q($modulePosition));
								$db->setQuery($query);
								$position = $db->loadResult();
								$position++;

								$query = $db->getQuery(true);
								$query->update($db->qn('#__modules'))
									->set($db->qn('ordering').' = '.$db->q($position))
									->where($db->qn('module').' = '.$db->q('mod_'.$module));
								$db->setQuery($query);
								$db->query();
							}

							// C. Link to all pages
							$query = $db->getQuery(true);
							$query->select('id')->from($db->qn('#__modules'))
								->where($db->qn('module').' = '.$db->q('mod_'.$module));
							$db->setQuery($query);
							$moduleid = $db->loadResult();

							$query = $db->getQuery(true);
							$query->select('*')->from($db->qn('#__modules_menu'))
								->where($db->qn('moduleid').' = '.$db->q($moduleid));
							$db->setQuery($query);
							$assignments = $db->loadObjectList();
							$isAssigned = !empty($assignments);
							if(!$isAssigned) {
								$o = (object)array(
									'moduleid'	=> $moduleid,
									'menuid'	=> 0
								);
								$db->insertObject('#__modules_menu', $o);
							}
						}
					}
			}
		}
		
		// Plugins installation
		if(count($this->installation_queue['plugins'])) {
			foreach($this->installation_queue['plugins'] as $folder => $plugins) {
				if(count($plugins)) 
				foreach($plugins as $plugin => $published) {
					$path = "$src/plugins/$folder/$plugin";
					if(!is_dir($path)) {
						$path = "$src/plugins/$folder/plg_$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/plg_$plugin";
					}
					if(!is_dir($path)) continue;

					// Was the plugin already installed?
					$query = $db->getQuery(true)
						->select('COUNT(*)')
						->from($db->qn('#__extensions'))
						->where('( '.($db->qn('name').' = '.$db->q($plugin)) .' OR '. ($db->qn('element').' = '.$db->q($plugin)) .' )')
						->where($db->qn('folder').' = '.$db->q($folder));
					$db->setQuery($query);
					$count = $db->loadResult();

					$installer = new JInstaller;
					$result = $installer->install($path);

					$status->plugins[] = array('name'=>$plugin,'group'=>$folder, 'result'=>$result,'status'=>$published);
					

					if($published && !$count) {
						$query = $db->getQuery(true)
							->update($db->qn('#__extensions'))
							->set($db->qn('enabled').' = '.$db->q('1'))							
							->where('( '.($db->qn('name').' = '.$db->q($plugin)) .' OR '. ($db->qn('element').' = '.$db->q($plugin)) .' )')
							->where($db->qn('folder').' = '.$db->q($folder));
						$db->setQuery($query);
						$db->query();
					}
				}
			}
		}

 
		return $status;
	}


	private function _installFOF($parent)
	{
		 $src = $parent->getParent()->getPath('source');
			// Install the FOF framework
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.date');

		/*$source = $src.'/fof';*/
		//changed by manoj
		$source = $src.DS.'tj_lib_fof';
		if(!defined('JPATH_LIBRARIES')) {
			$target = JPATH_ROOT.DS.'libraries'.DS.'fof';
		} else {
			$target = JPATH_LIBRARIES.DS.'fof';
		}
		$haveToInstallFOF = false;
		if(!JFolder::exists($target)) {
			$haveToInstallFOF = true;
		} else {
			$fofVersion = array();
			if(JFile::exists($target.DS.'version.txt')) {
				$rawData = JFile::read($target.DS.'version.txt');
				$info = explode("\n", $rawData);
				$fofVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
				);
			} else {
				$fofVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new JDate('2011-01-01')
				);
			}
			$rawData = JFile::read($source.DS.'version.txt');
			$info = explode("\n", $rawData);
			$fofVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new JDate(trim($info[1]))
			);

			$haveToInstallFOF = $fofVersion['package']['date']->toUNIX() > $fofVersion['installed']['date']->toUNIX();
		}
		$installedFOF = false;
		if($haveToInstallFOF) {
			$versionSource = 'package';
			$installer = new JInstaller;
			$installedFOF = $installer->install($source);
		} else {
			$versionSource = 'installed';
		}

		if(!isset($fofVersion)) {
			$fofVersion = array();
			if(JFile::exists($target.DS.'version.txt')) {
				$rawData = JFile::read($target.DS.'version.txt');
				$info = explode("\n", $rawData);
				$fofVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
				);
			} else {
				$fofVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new JDate('2011-01-01')
				);
			}
			$rawData = JFile::read($source.DS.'version.txt');
			$info = explode("\n", $rawData);
			$fofVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new JDate(trim($info[1]))
			);
			$versionSource = 'installed';
		}

		if(!($fofVersion[$versionSource]['date'] instanceof JDate)) {
			$fofVersion[$versionSource]['date'] = new JDate();
		}

		return array(
			'required'	=> $haveToInstallFOF,
			'installed'	=> $installedFOF,
			'version'	=> $fofVersion[$versionSource]['version'],
			'date'		=> $fofVersion[$versionSource]['date']->format('Y-m-d'),
		);
	}

	private function _installStraper($parent)
	{
		$src = $parent->getParent()->getPath('source');

		// Install the FOF framework
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.date');
		$source = $src.DS.'strapper';
		$target = JPATH_ROOT.DS.'media'.DS.'techjoomla_strapper';

		$haveToInstallStraper = false;
		if(!JFolder::exists($target)) {
			$haveToInstallStraper = true;
		} else {
			$straperVersion = array();
			if(JFile::exists($target.DS.'version.txt')) {
				$rawData = JFile::read($target.DS.'version.txt');
				$info = explode("\n", $rawData);
				$straperVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
				);
			} else {
				$straperVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new JDate('2011-01-01')
				);
			}
			$rawData = JFile::read($source.DS.'version.txt');
			$info = explode("\n", $rawData);
			$straperVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new JDate(trim($info[1]))
			);

			$haveToInstallStraper = $straperVersion['package']['date']->toUNIX() > $straperVersion['installed']['date']->toUNIX();
		}

		$installedStraper = false;
		if($haveToInstallStraper) {
			$versionSource = 'package';
			$installer = new JInstaller;
			$installedStraper = $installer->install($source);
		} else {
			$versionSource = 'installed';
		}

		if(!isset($straperVersion)) {
			$straperVersion = array();
			if(JFile::exists($target.DS.'version.txt')) {
				$rawData = JFile::read($target.DS.'version.txt');
				$info = explode("\n", $rawData);
				$straperVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
				);
			} else {
				$straperVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new JDate('2011-01-01')
				);
			}
			$rawData = JFile::read($source.DS.'version.txt');
			$info = explode("\n", $rawData);
			$straperVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new JDate(trim($info[1]))
			);
			$versionSource = 'installed';
		}

		if(!($straperVersion[$versionSource]['date'] instanceof JDate)) {
			$straperVersion[$versionSource]['date'] = new JDate();
		}

		return array(
			'required'	=> $haveToInstallStraper,
			'installed'	=> $installedStraper,
			'version'	=> $straperVersion[$versionSource]['version'],
			'date'		=> $straperVersion[$versionSource]['date']->format('Y-m-d'),
		);
	}

	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		// $parent is the class calling this method
		$this->installSqlFiles($parent);
		//$this->migrateTables();		
	}
	function installSqlFiles($parent)
	{
		$db = JFactory::getDBO();		 
		// Obviously you may have to change the path and name if your installation SQL file ;)
		if(method_exists($parent, 'extension_root')) {
			$sqlfile = $parent->getPath('extension_root').DS.'admin'.DS.'sql'.DS.'install.sql';
		} else {
			$sqlfile = $parent->getParent()->getPath('extension_root').DS.'sql'.DS.'install.sql';
		}
		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);
		if ($buffer !== false) {
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (count($queries) != 0) {
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
							return false;
						}
					}
				}
			}
		}					
		
		
	}

	
	/**
	 * Uninstalls subextensions (modules, plugins) bundled with the main extension
	 * 
	 * @param JInstaller $parent 
	 * @return JObject The subextension uninstallation status
	 */
	private function _uninstallSubextensions($parent)
	{
		jimport('joomla.installer.installer');
		
		$db =  JFactory::getDBO();
		
		$status = new JObject();
		$status->modules = array();
		$status->plugins = array();
		
		$src = $parent->getParent()->getPath('source');

		// Modules uninstallation
		if(count($this->uninstall_queue['modules'])) {
			foreach($this->uninstall_queue['modules'] as $folder => $modules) {
				if(count($modules)) foreach($modules as $module => $modulePreferences) {
					// Find the module ID
					$sql = $db->getQuery(true)
						->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('element').' = '.$db->q('mod_'.$module))
						->where($db->qn('type').' = '.$db->q('module'));
					$db->setQuery($sql);
					$id = $db->loadResult();
					// Uninstall the module
					if($id) {
						$installer = new JInstaller;
						$result = $installer->uninstall('module',$id,1);
						$status->modules[] = array(
							'name'=>'mod_'.$module,
							'client'=>$folder,
							'result'=>$result
						);
					}
				}
			}
		}

		// Plugins uninstallation
		if(count($this->uninstall_queue['plugins'])) {
			foreach($this->uninstall_queue['plugins'] as $folder => $plugins) {
				if(count($plugins)) foreach($plugins as $plugin => $published) {
					$sql = $db->getQuery(true)
						->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('type').' = '.$db->q('plugin'))
						->where($db->qn('element').' = '.$db->q($plugin))
						->where($db->qn('folder').' = '.$db->q($folder));
					$db->setQuery($sql);
					
					$id = $db->loadResult();
					if($id)
					{
						$installer = new JInstaller;
						$result = $installer->uninstall('plugin',$id);
						$status->plugins[] = array(
							'name'=>'plg_'.$plugin,
							'group'=>$folder,
							'result'=>$result
						);
					}			
				}
			}
		}
		
		return $status;
	}
		
	private function _renderPostUninstallation($status, $parent)
	{
?>
<?php $rows = 0;?>
<h2><?php echo JText::_('ewallet Uninstallation Status'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'ewallet '.JText::_('Component'); ?></td>
			<td><strong style="color: green"><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
		<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('Module'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong style="color: <?php echo ($module['result'])? "green" : "red"?>"><?php echo ($module['result'])?JText::_('Removed'):JText::_('Not removed'); ?></strong></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		<?php if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong style="color: <?php echo ($plugin['result'])? "green" : "red"?>"><?php echo ($plugin['result'])?JText::_('Removed'):JText::_('Not removed'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
<?php
	}
	
	/**
	 * Runs on uninstallation
	 * 
	 * @param JInstaller $parent 
	 */
	function uninstall($parent)
	{
		// Uninstall subextensions
		$status = $this->_uninstallSubextensions($parent);
		
		// Show the post-uninstallation page
		$this->_renderPostUninstallation($status, $parent);
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	 function update($parent) {
	 	$this->componentStatus="update";
	 	$this->installSqlFiles($parent);		 		
		$this->fix_db_on_update();	
		
		
	} // end of update

	function runSQL($parent,$sqlfile) 
	{
		$db = JFactory::getDBO();
		// Obviously you may have to change the path and name if your installation SQL file ;)
		if(method_exists($parent, 'extension_root')) {
			$sqlfile = $parent->getPath('extension_root').DS.'admin'.DS.'sql'.DS.$sqlfile;
		} else {
			$sqlfile = $parent->getParent()->getPath('extension_root').DS.'sql'.DS.$sqlfile;
		}
		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);
		if ($buffer !== false) {
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (count($queries) != 0) {
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
							return false;
						}
					}
				}
			}
		}
	}//end run sql

	//since version 1.0.2
	function fix_db_on_update()
	{
		
	}// end of fix_db_on_update	

	/*	Tag line, version etc
	 * 
	 * 
	 * */
	function taglinMsg()
	{		

		echo JText::_(	"<h4>Thank you for installing ewallet by Techjoomla, the powerful ecommerce component for Joomla! </h4>");
	//	echo JText::_('<br/><h1>ewallet - Quick, Simple Ecommerce For Joomla</h1>	');
		
		
			
		/// version check
		$path=JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_ewallet'.DS.'ewallet.xml'; 
		$xml =JFactory::getXML($path );
		$oldversion=$xml->version; 
			
		require_once(JPATH_ROOT.DS.'components'.DS.'com_ewallet'.DS.'helper.php');		
		$helperobj= new comewalletHelper();
		$latestversion=$helperobj->getVersion();
		//if($latestversion != $oldversion)
		if(version_compare($oldversion, $latestversion, 'lt'))
		{
			echo "<span id='NewVersion' style='padding-top: 5px; color: red; font-weight: bold; padding-left: 5px;'>". JText::_("It seems that you have downloaded an older version. Latest Version is : "). $latestversion ."</span>";
		}
		
		
	} // end of tagline msg
	
	/*
	 * @return : this function return array of column names
	 * */
	function getColumns($table)
	{
		$db = JFactory::getDBO();
		
		$field_array = array();	
		$query = "SHOW COLUMNS FROM ".$table;
		$db->setQuery($query);
		$columns = $db->loadobjectlist();
		
		for ($i = 0; $i < count($columns); $i++) {
			$field_array[] = $columns[$i]->Field;
		}
		return $field_array;
	}
	/* Migration start (user item_id insted of product_id,parent) */

}//end class
?>

