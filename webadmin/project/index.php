<?php
include_once __DIR__ . '/project_common.inc.php';

$action = $posted['action'];

//Update Actions
switch ($action) {
    case 'do_add':
        unset($posted['project']['project_id']);
    case 'do_edit':
        if ('' != $posted['site_id']) {
            $posted['project']['site_id'] = $posted['site_id'];
        }

        $project_id           = $posted['project']['project_id'];
        $new_project          = projectFromPost($posted['project']);
        $project_id           = projectToDatabase($new_project);
        $posted['project_id'] = $project_id;

        $action = 'list';
        break;

    case 'do_chapter_add':
        unset($posted['chapter']['chapter_id']);
        $posted['chapter']['chapter_rank'] = count(projectChapterSearch(array(
            'project_id' => $posted['chapter']['project_id'],
        ))) + 1;
    case 'do_chapter_edit':
        $chapter_id = $posted['chapter']['chapter_id'];

        if (!isset($posted['chapter'])) {
            $posted['chapter'] = array();
        }

        $new_chapter          = projectChapterFromPost($posted['chapter']);
        $chapter_id           = projectChapterToDatabase($new_chapter);
        $posted['chapter_id'] = $chapter_id;
        $posted['project_id'] = $new_chapter['project_id'];

        if (isset($posted['page'])) {
            $page_id                      = $posted['page']['page_id'];
            $posted['page']['chapter_id'] = $chapter_id;
            $new_page                     = projectPageFromPost($posted['page']);
            $page_id                      = projectPageToDatabase($new_page);
        }

        if (is_array($posted['page_id'])) {
            foreach ($posted['page_id'] as $key => $page_id) {
                $rank = max(1, $posted['page_rank'][$key]);

                if (projectPageExists($page_id)) {
                    $DB->query("UPDATE `highwaygroup`.project_pages SET page_rank=" . intval($rank) . " WHERE page_id=" . intval($page_id) . " LIMIT 1");
                }

            }
        }

        if (isset($posted['projectnotes'])) {
            if (!empty($posted['projectnotes']['type']) && !empty($posted['projectnotes']['notes'])) {
                $posted['projectnotes']['chapter_id'] = $chapter_id;
                $new_note                             = projectNoteFromPost($posted['projectnotes']);
                $note_id                              = projectNoteToDatabase($new_note);
            }
        }

        $action            = 'chapter_edit';
        $posted['updated'] = "true";
        break;

    case 'do_chapter_delete':
        $chapter_id = $posted['chapter_id'];
        projectChapterDelete($chapter_id);
        $action = 'view';
        break;

    case 'do_chapter_moveup':
    case 'do_chapter_movedown':
        $chapter_id = $posted['chapter_id'];
        $chapter    = projectChapterFromDatabase($chapter_id);
        $project_id = $chapter['project_id'];

        $chapter_list = projectChapterSearch(array('project_id' => $project_id));
        $old_index    = array_search($chapter_id, $chapter_list);

        $new_index = max(0, $old_index - 1);
        if ('do_chapter_movedown' == $action) {
            $new_index = min(count($chapter_list) - 1, $old_index + 1);
        }

        $chapter_list[$old_index] = $chapter_list[$new_index];
        $chapter_list[$new_index] = $chapter_id;

        foreach ($chapter_list as $rank => $chapter_id) {
            $DB->query("UPDATE project_chapters SET chapter_rank=" . ($rank + 1) . " WHERE chapter_id=" . intval($chapter_id) . " LIMIT 1");
        }

        $action = 'view';
        break;

    case 'unlock':
        projectSetLock($posted['chapter_id']);
        $action = 'view';
        break;
}

$get_vars  = $posted;
$keep_only = array('order_by', 'search_for', 'page_id', 'order', 'project_id', 'chapter_id', 'site_id', 'updated');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

//Redirect Actions
switch ($action) {
    case 'add':
        unset($get_vars['project_id']);
        header('Location: project_add.php?' . http_build_query($get_vars));
        break;
    case 'edit':
        header('Location: project_edit.php?' . http_build_query($get_vars));
        break;
    case 'delete':
        header('Location: project_delete.php?' . http_build_query($get_vars));
        break;
    case 'view':
        header('Location: project_view.php?' . http_build_query($get_vars));
        break;

    case 'chapter_add':
        unset($get_vars['chapter_id']);
        header('Location: project_chapter_add.php?' . http_build_query($get_vars));
        break;
    case 'chapter_edit':
        projectSetLock($get_vars['chapter_id'], $_GLOBALS['staff']['id']);
        header('Location: project_chapter_edit.php?' . http_build_query($get_vars));
        break;
    case 'chapter_delete':
        header('Location: project_chapter_delete.php?' . http_build_query($get_vars));
        break;
    case 'chapter_view':
        if (is_array($posted['chapter_list'])) {
            $get_vars['chapter_id'] = implode('|', $posted['chapter_list']);
        }

        header('Location: project_chapter_view.php?' . http_build_query($get_vars));
        break;

    case 'page_add':
        unset($get_vars['page_id']);
        header('Location: project_page_add.php?' . http_build_query($get_vars));
        break;
    case 'page_edit':
        header('Location: project_page_edit.php?' . http_build_query($get_vars));
        break;
    case 'page_delete':
        header('Location: project_page_delete.php?' . http_build_query($get_vars));
        break;
    case 'page_view':
        header('Location: project_page_view.php?' . http_build_query($get_vars));
        break;

    case 'summary':
        if (is_array($posted['chapter_list'])) {
            $get_vars['chapter_id'] = implode('|', $posted['chapter_list']);
        }

        header('Location: project_summary.php?' . http_build_query($get_vars));
        break;
    case 'search':
        header('Location: project_search.php?' . http_build_query($get_vars));
        break;
    default:
    case 'list':
        header('Location: project_list.php?' . http_build_query($get_vars));
        break;
}

die();
