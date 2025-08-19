<?php
require_once '../config/db.php';
session_start();
$options=[];
$correct = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    print_r($_SESSION);
    if (isset($_POST['add']) && isset($_POST['edit'])) {
        $id = $_POST['id'];
        
        echo $id;
        $i = 0;
        $optionsResult = $conn->query("SELECT * FROM gq_options WHERE question_id=$id");
        while ($opt = $optionsResult->fetch_assoc()) {
            if (count($options) < 4) {
                $options[] = [$opt['option_text'], $opt['is_correct'], $opt['id']];
                // $options_ids[] = $opt['id'];
            }
            if ($opt['is_correct']) {
                $correct = $i;
            }
            $i++;
        }
        $stmt = $conn->prepare(
            "UPDATE gq_questions SET session_id=?, question_text=?, question_type=?, marks=?, is_final_paper=? WHERE id=?"
        );

        $stmt->bind_param(
            'issiii',
            $_POST['session_id'],
            $_POST['text'],
            $_POST['type'],
            $_POST['marks'],
            $_POST['final'],
            $id
        );

        if ($stmt->execute()) {
            
        } else {
            echo "Error updating question: " . $stmt->error;
        }

        // Update options
        // print_r($options_ids);
        // print_r($options);
        echo $_POST['correct'] . "<br>Hi";
        foreach ($_POST['options'] as $i => $txt) {
            echo "Option $i: $txt ".$options[$i][2] ."<br>";
            echo "Correct: " . (isset($_POST['correct']) && $_POST['correct'] == $i ? 1 : 0) . "<br>";
            $correct = isset($_POST['correct']) && $_POST['correct'] == $i ? 1 : 0;
            // echo ''. $correct .'';
            if (isset($options[$i][2])) {
                // Update existing option
                $stmt = $conn->prepare(
                    "UPDATE gq_options SET option_text=?, is_correct=? WHERE id=?"
                );
                $stmt->bind_param('sii', $txt, $correct, $options[$i][2]);
            } else {
                echo "Adding new option";
            }
            header('Location: '.BASE_URL.'/pages/admin/questions_manager.php');
            // exit();
            if (!$stmt->execute()) {
                echo "Error updating options: " . $stmt->error;
            }
        }
    } else {
        // echo "No POST data received.";
        if (isset($_POST['add'])) {
            // 1. insert question
            $stmt = $conn->prepare(
                "INSERT INTO gq_questions (session_id, question_text, question_type, marks, is_final_paper)
                VALUES (?,?,?,?,?)"
            );
            $stmt->bind_param(
                'issii',
                $_POST['session_id'],
                $_POST['text'],
                $_POST['type'],
                $_POST['marks'],
                $_POST['final']
            );
            $stmt->execute();
            $qid = $conn->insert_id;

            // 2. insert options

            foreach ($_POST['options'] as $i => $txt) {
                $correct = isset($_POST['correct']) && $_POST['correct'] == $i ? 1 : 0;
                $stmt = $conn->prepare(
                    "INSERT INTO gq_options (question_id, option_text, is_correct) VALUES (?,?,?)"
                );
                $stmt->bind_param('isi', $qid, $txt, $correct);
                $stmt->execute();
                $stmt->close();
            }
            header('Location: ' . BASE_URL . '/pages/admin/questions_manager.php');
            exit();
        }
        if (isset($_POST['delete'])) {
            $qid = (int)$_POST['delete'];
            // $conn->query("DELETE FROM gq_options WHERE question_id=$qid");
            // $conn->query("DELETE FROM gq_questions WHERE id=$qid");
            $stmt = $conn->prepare("DELETE FROM gq_questions WHERE id=?");
            $stmt->bind_param('i', $qid);
            $stmt->execute();
            header('Location: ' . BASE_URL . '/pages/admin/questions_manager.php');
            exit();
        }
    }
}