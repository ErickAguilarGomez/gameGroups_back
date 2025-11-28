<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateSpGetQuestionariesWithStatsFinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        DROP PROCEDURE IF EXISTS sp_get_questionaries_with_stats;
        
        CREATE PROCEDURE sp_get_questionaries_with_stats(IN status_param VARCHAR(20), IN user_id_param BIGINT)
        BEGIN
            WITH FilteredQuestionaries AS (
                SELECT *
                FROM questionaries
                WHERE deleted_at IS NULL
                AND (
                    (status_param = 'active' AND DATE(end_date) >= CURDATE()) OR
                    (status_param = 'inactive' AND DATE(end_date) < CURDATE()) OR
                    (status_param IS NULL OR status_param = '')
                )
                UNION
                SELECT *
                FROM questionaries
                WHERE deleted_at IS NOT NULL
                AND status_param = 'inactive'
            ),
            QuestionaryTotals AS (
                SELECT 
                    qr.questionary_id,
                    COUNT(qr.id) as total_responses
                FROM questionaries_response qr
                WHERE qr.deleted_at IS NULL
                GROUP BY qr.questionary_id
            ),
            QuestionStats AS (
                SELECT 
                    qr.question_id,
                    COUNT(qr.id) as vote_count
                FROM questionaries_response qr
                WHERE qr.deleted_at IS NULL
                GROUP BY qr.question_id
            ),
            UserResponses AS (
                SELECT 
                    qr.id as response_id,
                    qr.question_id,
                    qr.questionary_id
                FROM questionaries_response qr
                WHERE qr.user_id = user_id_param
                AND qr.deleted_at IS NULL
                -- Ensure only one response per questionnaire is picked if multiple exist
                GROUP BY qr.questionary_id, qr.id, qr.question_id
            )
            SELECT 
                q.id,
                q.title,
                q.start_date,
                q.end_date,
                q.created_at,
                q.created_by,
                q.updated_by,
                COALESCE(qt.total_responses, 0) as total_responses,
                ur.response_id as user_response_id,
                ur.question_id as user_question_id,
                IF(COUNT(qs.id) = 0, '[]', JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', qs.id,
                        'question', qs.question,
                        'created_by', qs.created_by,
                        'updated_by', qs.updated_by,
                        'counter', COALESCE(q_stats.vote_count, 0),
                        'percentage', CASE 
                            WHEN COALESCE(qt.total_responses, 0) > 0 THEN ROUND((COALESCE(q_stats.vote_count, 0) / qt.total_responses) * 100, 2)
                            ELSE 0
                        END
                    )
                )) as questions
            FROM FilteredQuestionaries q
            LEFT JOIN QuestionaryTotals qt ON q.id = qt.questionary_id
            LEFT JOIN questions qs ON q.id = qs.questionary_id
            LEFT JOIN QuestionStats q_stats ON qs.id = q_stats.question_id
            LEFT JOIN UserResponses ur ON q.id = ur.questionary_id
            GROUP BY q.id, q.title, q.start_date, q.end_date, q.created_at, q.created_by, q.updated_by, qt.total_responses, ur.response_id, ur.question_id
            ORDER BY q.created_at DESC;
        END;
        ";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_questionaries_with_stats");
    }
}
