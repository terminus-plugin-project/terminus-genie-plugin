<?php

namespace TerminusPluginProject\Genie\Commands;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Symfony\Component\Console\Input\InputOption;

class GenieCommand extends TerminusCommand implements SiteAwareInterface
{
  use SiteAwareTrait;

  /**
   * Runs a terminus command against multiple sites or environments.
   *
   * @authorize
   *
   * @command genie
   *
   * @option name Name filter
   * @option org Organization filter; "all" or an organization's name, label, or ID
   * @option owner Owner filter; "me" or user UUID
   * @option team Team-only filter
   * @option env Environment to run against; either "dev", "test", or "live"
   *
   * @usage terminus genie -- command
   */
  public function genie(array $terminus_command, $options = ['name' => null, 'org' => 'all', 'owner' => null, 'team' => false, 'env' => InputOption::VALUE_OPTIONAL])
  {
    $user = $this->session()->getUser();


    $this->sites()->fetch(
      [
        'org_id' => (isset($options['org']) && ($options['org'] !== 'all')) ? $user->getOrganizationMemberships()->get($options['org'])->getOrganization()->id : null,
        'team_only' => isset($options['team']) ? $options['team'] : false,
      ]
    );

    if (isset($options['name']) && !is_null($name = $options['name'])) {
      $this->sites()->filterByName($name);
    }

    if (isset($options['owner']) && !is_null($owner = $options['owner'])) {
      if ($owner == 'me') {
        $owner = $user->id;
      }
      $this->sites()->filterByOwner($owner);
    }

    $sites = $this->sites->serialize();

    if (empty($sites)) {
      $this->log()->notice('No sites matched your selected filters.');
    }

    foreach ($this->sites->serialize() as $site) {
      $this->executeCommand($site, $options['env'], $terminus_command);
    }
  }

  /**
   * Execute a specific terminus command.
   */
  private function executeCommand($site, $env, $command) {

    // We need to split the $command up so we can add a site and possibly env.
    $first_command = array_shift($command);

    if (!in_array($env, ['dev', 'test', 'live'])) {
      $full_command = array_merge([$first_command], [$site['name']], $command);
    }
    else {
      $full_command = array_merge([$first_command], [$site['name'] . "." . $env], $command);
    }

    passthru($_SERVER['argv'][0] . " " . $this->getCommandLine($full_command));
  }

  /**
   * Consolidate the command arguments into a single command.
   */
  private function getCommandLine($command_args) {
    return implode(" ", $command_args);
  }
}