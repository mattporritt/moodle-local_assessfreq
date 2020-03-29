<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A scheduled task to generate data used in plugin reports.
 *
 * @package    local_assessfreq
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_assessfreq\task;

use core\task\scheduled_task;

defined('MOODLE_INTERNAL') || die();

/**
 * A scheduled task to generate data used in plugin reports.
 *
 * @package    local_assessfreq
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_process extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('task:dataprocess', 'local_assessfreq');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        mtrace('local_assessfreq: Processing event data');
        $now = time();
        $frequency = new \local_assessfreq\frequency();

        // We dont't want to reprocess data.
        // We also don't care if a due date for an event is changed in the past.
        // So get latest data from DB and use it as the start point.

        // Due dates may have changed since we last ran report. So delete all events in DB later than today and replace them.
        $frequency->delete_events($now); // Delete event records greaer than now.
        $frequency->process_site_events($now); // Process records in the future

        // TODO: Add stuff to cache.
    }
}