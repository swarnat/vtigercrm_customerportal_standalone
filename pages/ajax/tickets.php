<?php
$connection = getConnection();

switch($_REQUEST["operation"]) {
    case "get_children":
        $tickets = $connection->getRelated($_SESSION["cp_user"]["module"], $_SESSION["cp_user"]["id"], "HelpDesk");
        $tree = array();
        foreach($tickets as $ticket) {
            if(empty($tree[$ticket["status"]])) {
                $tree[$ticket["status"]] = array();
            }

            $tree[$ticket["status"]][] = array($ticket["id"], "[".$ticket["priority"]."] ".$ticket["title"]);

        }

        $jsonResult = array();
        foreach($tree as $status => $tickets) {
            $children = array();
            foreach($tickets as $ticket) {
                $children[] = array(
                    "data" => $ticket[1],
                    "attr" => array("id" => "ticket_".$ticket[0])
                );
            }

            $jsonResult[] = array(
                "data" => $status,
                "children" => $children,
                "state" => "open"
            );
        }
    break;
    case "create":
        #Vtiger_Customerportal::setDebug(true);
        $fields = $connection->getFields("HelpDesk", true);

        require_once 'HTML/QuickForm2.php';
        require_once 'HTML/QuickForm2/Renderer.php';

        HTML_QuickForm2_Renderer::register("floating", "HTML_Customerportal_Renderer_Floating");

        $form = new HTML_QuickForm2('elements');
        $form->setAttribute('action', "#");

        #$form->addDataSource(new HTML_QuickForm2_DataSource_Array($ticket));


        $saveable = addFields($fields, $form);

        $form->addElement(
            'hidden', "create"
        )->setValue("1");

        if($saveable) {
            $form->addElement(
                'submit', 'submit', array('value' => 'Create this ticket!')
            );
        }

        if ('POST' == $_SERVER['REQUEST_METHOD'] && $form->isSubmitted()) {
            echo "<pre>\n";

            $values = $form->getValue();
            $connection->setRecord("Contacts", $_SESSION["cp_user"]["id"], $values);
            echo "</pre>\n<hr />";
            // let's freeze the form and remove the reset button
        #    $fsButton->removeChild($testReset);
         #   $form->toggleFrozen(true);
        }

        $renderer = HTML_QuickForm2_Renderer::factory('floating');
        $form->render($renderer);
        // Output javascript libraries, needed by hierselect
        #echo $renderer->getJavascriptBuilder()->getLibraries(true, true);
        echo $renderer;
        break;
    case "content":
        $fields = $connection->getFields("HelpDesk");

        require_once 'HTML/QuickForm2.php';
        require_once 'HTML/QuickForm2/Renderer.php';

        HTML_QuickForm2_Renderer::register("floating", "HTML_Customerportal_Renderer_Floating");

        $ticketID = explode("_", $_POST["id"]);
        $ticket = $connection->getRecord("Helpdesk", $ticketID[1]);

        $form = new HTML_QuickForm2('elements');
        $form->setAttribute('action', "#");

        $form->addDataSource(new HTML_QuickForm2_DataSource_Array($ticket));

        $saveable = addFields($fields, $form);

        if($saveable) {
            $form->addElement(
                'submit', 'submit', array('value' => 'Save your Values!')
            );
        }

        if ('POST' == $_SERVER['REQUEST_METHOD'] && $form->isSubmitted()) {
            echo "<pre>\n";

            $values = $form->getValue();
            $connection->setRecord("Contacts", $_SESSION["cp_user"]["id"], $values);
            echo "</pre>\n<hr />";
            // let's freeze the form and remove the reset button
        #    $fsButton->removeChild($testReset);
         #   $form->toggleFrozen(true);
        }

        $renderer = HTML_QuickForm2_Renderer::factory('floating');
        $form->render($renderer);
        // Output javascript libraries, needed by hierselect
        #echo $renderer->getJavascriptBuilder()->getLibraries(true, true);
        echo $renderer;

        $comments = $connection->getComments("HelpDesk", $ticketID[1]);

        $hView->assign("module", "HelpDesk");
        $hView->assign("recordId", $ticketID[1]);
        $hView->assign("writeComment", true);
        $hView->assign("comments", $comments);
        echo $hView->render("comments.phtml");


        $documents = $connection->getRelated("HelpDesk", $ticketID[1], "Documents");

        echo "<h3>Files attached to this ticket</h3>";
        $hView->assign("documents", $documents);
        echo $hView->render("files.phtml");

        $hView->assign("module", "HelpDesk");
        $hView->assign("recordID", $ticketID[1]);
        echo $hView->render("fileupload.phtml");

#        echo $hView->render("pages/ticket.phtml");
        break;
}

if(!empty($jsonResult)) {
    echo json_encode($jsonResult);
}