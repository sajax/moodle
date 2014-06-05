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
 */

include_once($CFG->dirroot . '/course/renderer.php');
class theme_rsme_standard_v3_core_course_renderer extends core_course_renderer {
    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param course_in_list|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG;
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';
        $classes = trim('coursebox clearfix '. $additionalclasses);
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h3';
        } else {
            $classes .= ' collapsed';
            $nametag = 'div';
        }
        
        // .coursebox
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));

        $content .= html_writer::start_tag('div', array('class' => 'info'));

        // course name
        $coursename = $chelper->get_course_formatted_name($course);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                            $coursename, array('class' => $course->visible ? '' : 'dimmed'));
        $content .= html_writer::tag($nametag, $coursenamelink, array('class' => 'coursename'));
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('div', array('class' => 'moreinfo'));
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()) {
                $url = new moodle_url('/course/info.php', array('id' => $course->id));
                $image = html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/info'),
                    'alt' => $this->strings->summary));
                $content .= html_writer::link($url, $image, array('title' => $this->strings->summary));
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }
        $content .= html_writer::end_tag('div'); // .moreinfo

        // print enrolmenticons
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
            foreach ($icons as $pix_icon) {
                $content .= $this->render($pix_icon);
            }
            $content .= html_writer::end_tag('div'); // .enrolmenticons
        }

        $content .= html_writer::end_tag('div'); // .info

        $content .= html_writer::start_tag('div', array('class' => 'content'));
        $content .= $this->coursecat_coursebox_content($chelper, $course);
        $content .= html_writer::end_tag('div'); // .content

        $content .= html_writer::end_tag('div'); // .coursebox
        
        /**
        * BEGIN NEW COURSE INFO BOX
        */
        
        // course name
        //$coursename = $chelper->get_course_formatted_name($course);
        //$coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
        //                                    $coursename, array('class' => $course->visible ? '' : 'dimmed'));
        
        $contentimages = $contentfiles = '';
        $img_count = 0;
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            
            if ($isimage && $img_count == 0) {
                $contentimages .= $url;
                $img_count++;
            } else {
                $image = $this->output->pix_icon(file_file_icon($file, 16), $file->get_filename(), 'moodle');
                $filename = html_writer::tag('span', $image, array('class' => '')).
                        html_writer::tag('span', $file->get_filename(), array('class' => ''));
                $contentfiles .= html_writer::tag('span',
                        html_writer::link($url, $filename),
                        array('class' => 'coursefile fp-filename-icon'));
            }
        }
        
        $courseimage = (!empty($contentimages)) ? $contentimages : $this->output->pix_url('logos/eng_logo_grey', 'theme');
        
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));
        
        $content .= html_writer::start_tag('div', array('class' => 'course-image'));
        
        $content .= html_writer::empty_tag('img', array('src'=>$courseimage, 'alt'=>$coursename, 'class' => 'course-thumb'));
        
        // Course Summary Files
        if ($contentfiles) {
            
            $content .= html_writer::start_tag('div', array('class' => 'course-sidebar'));
            $content .= html_writer::tag('div', 'Course Admin', array('class' => 'course-sidebar-heading'));

            $content .= $contentfiles;

            $content .= html_writer::end_tag('div'); // .course-files
            
            if ($course->has_course_contacts()) {
                $content .= html_writer::start_tag('div', array('class' => 'course-sidebar'));
                $content .= html_writer::tag('div', 'Course Contacts', array('class' => 'course-sidebar-heading'));
                $content .= html_writer::start_tag('ul', array('class' => 'teachers'));
                foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                    $name = html_writer::link(new moodle_url('/user/view.php',
                                    array('id' => $userid, 'course' => SITEID)),
                                $coursecontact['username']);
                    $content .= html_writer::tag('li', $name);
                }
                $content .= html_writer::end_tag('ul'); // .teachers
                $content .= html_writer::end_tag('div'); // .course-box-bar
            }
        }
        
        $content .= html_writer::end_tag('div'); // .course-image
        
        $content .= html_writer::start_tag('div', array('class' => 'course-details'));
        
        $content .= html_writer::tag($nametag, $coursenamelink, array('class' => 'coursename'));
    
        // Course Summary Text
        $content .= html_writer::tag('div', $chelper->get_course_formatted_summary($course,
                    array('overflowdiv' => true, 'noclean' => true, 'para' => false)));
        
        
        $content .= html_writer::end_tag('div'); // .course-details
        $content .= html_writer::end_tag('div'); // .coursebox
        return $content;
    }
}