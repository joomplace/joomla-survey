<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerReport extends JControllerForm {

    public function pdf() {
		require_once(JPATH_COMPONENT_ADMINISTRATOR . '/assets/tcpdf/sf_pdf.php');

		$id = JFactory::getApplication()->input->get('id', '0');
		$report = $this->getReport($id, true);

		$pdf_doc = new sf_pdf();
		$pdf = &$pdf_doc->_engine;

		$pdf->getAliasNbPages();
		$pdf->AddPage();

		$fontFamily = 'freesans';

				$pdf->SetFontSize(10);
				$pdf->setFont($fontFamily, 'B');
				$pdf->Write(5,JText::_('COM_SURVEYFORCE_SURVEY_INFORMATION'), '', 0);
				$pdf->Ln();$pdf->Ln();

				$pdf->SetFontSize(8);
				$pdf->Write(5,JText::_('COM_SURVEYFORCE_NAME').": ", '', 0);

				$pdf->setFont($fontFamily, 'B');
				$pdf->Write(5, $pdf_doc->cleanText($report['survey_info']->sf_name), '', 0);
				$pdf->Ln();

				$pdf->setFont($fontFamily, 'B');
				$pdf->Write(5,JText::_('COM_SURVEYFORCE_DESCRIPTION'), '', 0);

				$pdf->setFont($fontFamily, 'B');
				$pdf->Write(5, $pdf_doc->cleanText($report['survey_info']->sf_descr), '', 0);
				$pdf->Ln();

				$pdf->line( 15, $pdf->GetY(), 200, $pdf->GetY());
				$pdf->line( 15, $pdf->GetY()+2, 200, $pdf->GetY()+2);
				$pdf->Ln();

			$pdf->SetFontSize(10);
			$pdf->setFont($fontFamily, 'B');
			$pdf->Write(5,JText::_('COM_SURVEYFORCE_USER_INFORMATION'), '', 0);
			$pdf->Ln();

			$pdf->SetFontSize(8);
			$pdf->Write(5,JText::_('COM_SURVEYFORCE_START_AT').": ", '', 0);
			$pdf->Ln();

			$text_to_pdf = $report['start_data']->sf_time . (($report['start_data']->is_complete)?' (completed)':' (not completed)');
			$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
			$pdf->setFont($fontFamily, 'B');
			$pdf->Write(5, $text_to_pdf, '', 0);
			$pdf->Ln();

			$pdf->setFont($fontFamily, 'B');
			$pdf->Write(5,JText::_('COM_SURVEYFORCE_USER').": ", '', 0);

					$pdf->setFont($fontFamily, 'B');
					$text_to_pdf = '';
					switch($report['start_data']->usertype) {
						case '0': $text_to_pdf .= JText::_('COM_SURVEYFORCE_GUEST')." - "; break;
						case '1': $text_to_pdf .= JText::_('COM_SURVEYFORCE_REGISTERED_USER')." - "; break;
						case '2': $text_to_pdf .= JText::_('COM_SURVEYFORCE_INVITED_USER')." - "; break;
					}
					switch($report['start_data']->usertype) {
						case '0': $text_to_pdf .= JText::_('COM_SURVEYFORCE_ANONYMOUS'); break;
						case '1': $text_to_pdf .= $report['user_data']->username.", ".$report['user_data']->name." (".$report['user_data']->email.")"; break;
						case '2': $text_to_pdf .= $report['user_data']->name." ".$report['user_data']->lastname." (".$report['user_data']->email.")"; break;
					}
					$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
					$pdf->Write(5, $pdf_doc->cleanText($text_to_pdf), '', 0);
					$pdf->Ln();

					$pdf->line( 15, $pdf->GetY(), 200, $pdf->GetY());
					$pdf->line( 15, $pdf->GetY()+2, 200, $pdf->GetY()+2);
					$pdf->Ln();

						$pdf->setFont($fontFamily, 'B');
						$pdf->Write(5,JText::_('COM_SURVEYFORCE_USER_ANSWERS'), '', 0);
						$pdf->Ln();
						$pdf->line( 15, $pdf->GetY(), 200, $pdf->GetY());
						$pdf->Ln();
						$pdf->setFont($fontFamily, 'B');

						foreach ($report['questions'] as $qrow) {
							$text_to_pdf = $pdf_doc->cleanText($qrow['question']->sf_qtext);
							$pdf->SetFontSize(10);
							$pdf->Write(5, $text_to_pdf, '', 0);
							$pdf->Ln();

							switch ($qrow['question']->sf_qtype) {
								case 2:
									$text_to_pdf = '';
									foreach ($qrow['answer_data']['answers'] as $arow) {
										$img_ans = $arow['alt_text'] ? " - ".JText::_('COM_SURVEYFORCE_USER_CHOICE') : '';
										$text_to_pdf .= $arow['f_text'] . $img_ans . "\n";
									}
									$text_to_pdf 	= $pdf_doc->cleanText( $text_to_pdf );
									$pdf->SetFontSize(8);

									$pdf->Write(5, $text_to_pdf, '', 0);
									$pdf->Ln();
									break;
								case 3:
									$text_to_pdf = '';
								
									foreach ($qrow['answer_data']['answers']['answer'] as $arow) {	
										$img_ans = $arow['alt_text'] ? " - ".JText::_('COM_SURVEYFORCE_USER_CHOICE') : '';
										$text_to_pdf .= $arow['f_text'] . $img_ans . "\n";
									}
									$text_to_pdf 	= $pdf_doc->cleanText( $text_to_pdf );
									$pdf->SetFontSize(8);

									$pdf->Write(5, $text_to_pdf, '', 0);
									$pdf->Ln();
									break;
								case 1:	
									$alt_text = '';
									$sc_count = count($qrow['answer_data']['answers']['answer']);
									$i = 0;
									$text_to_pdf = JText::_('COM_SURVEYFORCE_SCALE').": " . $qrow['answer_data']['answers']['scale'] ;
									$text_to_pdf 	= $pdf_doc->cleanText( $text_to_pdf );
									$pdf->SetFontSize(8);
									$pdf->Write(5, $text_to_pdf, '', 0);
									$pdf->Ln();
								case 5:
								case 6:
								case 9:

									$text_to_pdf = '';
									foreach ($qrow['answer_data']['answers']['answer'] as $arow) {
										$text_to_pdf .= $arow['f_text'] . " - " . $arow['alt_text'] . "\n";
									}
									$text_to_pdf 	= $pdf_doc->cleanText( $text_to_pdf );
									$pdf->SetFontSize(8);
									$pdf->Write(5, $text_to_pdf, '', 0);
									$pdf->Ln();
									break;
								case 4:
									if (isset($qrow['answer_data']['answers'])){
										//$tmp = JText::_('COM_SURVEYFORCE_1ST_ANSWER');
										$sh_answrs_count = count($qrow['answer_data']['answers']);
										for($ii = 1; $ii <= $sh_answrs_count; $ii++) {
											/* if('nosense_code'){ */
											if ($ii == 2) $tmp = JText::_('COM_SURVEYFORCE_SECOND_ANSWER');
											elseif($ii == 3)	$tmp = JText::_('COM_SURVEYFORCE_THIRD_ANSWER');
											elseif ($ii > 3) $tmp = $ii.JText::_('COM_SURVEYFORCE_TH_ANSWER');
											/* } */

											foreach($qrow['answer_data']['answers'] as $answer) {
												$text_to_pdf = $tmp.($answer == ''?' '.JText::_('COM_SURVEYFORCE_NO_ANSWER'):$answer)."\n";
												$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
												$pdf->SetFontSize(8);
												$pdf->Write(5, $text_to_pdf, '', 0);
												$pdf->Ln();
												$tmp = -1;
											}
											if ($tmp != -1)	{
												$text_to_pdf = $tmp." ".JText::_('COM_SURVEYFORCE_NO_ANSWER')."\n";
												$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
												$pdf->SetFontSize(8);
												$pdf->Write(5, $text_to_pdf, '', 0);
												$pdf->Ln();
											}
										}
									}
									else {
										$text_to_pdf = '';
										foreach ($qrow['answer_data']['answers']['answer'] as $arow) {	
										$ans_txt = JText::_('COM_SURVEYFORCE_USER_CHOICE_FOR')." ".$arow->ans_field." ".JText::_('COM_SURVEYFORCE_FIELD')." - ".$arow->ans_txt;
										$text_to_pdf .= $ans_txt . "\n";
										}
										$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
										$pdf->SetFontSize(8);
										$pdf->Write(5, $text_to_pdf, '', 0);
										$pdf->Ln();

									}
									break;
								default:
									$text_to_pdf = $qrow['answer_data']['answer'] . "\n";
									$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
									$pdf->SetFontSize(8);
									$pdf->Write(5, $text_to_pdf, '', 0);
									$pdf->Ln();
									break;									
							}	
				
							
							if ($qrow['question']->sf_impscale) {
	
															
								$text_to_pdf = $qrow['answer_data']['imp_answers']->iscale_name;
								$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
								$pdf->SetFontSize(10);
								$pdf->Write(5, $text_to_pdf, '', 0);
								$pdf->Ln();
								

								$text_to_pdf = '';
					
								foreach ($qrow['answer_data']['imp_answers']->answer_imp as $answ) {
								
								$img_ans = $answ->alt_text ? " - ".JText::_('COM_SURVEYFORCE_USER_CHOICE') : '';
									
									$text_to_pdf .= $answ->f_text . $img_ans . "\n";									
						
								}
							
								//TODO: ANSWER_IMP ( Q->sf_impscale
								$text_to_pdf = $pdf_doc->cleanText($text_to_pdf);
								$pdf->SetFontSize(8);

								$pdf->Write(5, $text_to_pdf, '', 0);
								$pdf->Ln();
								
							}
							$pdf->line( 15, $pdf->GetY(), 200, $pdf->GetY());
						}
						$pdf->line( 15, $pdf->GetY(), 200, $pdf->GetY());

		$data = $pdf->Output('', 'S');

		@ob_end_clean();
		header("Content-type: application/pdf");
		header("Content-Length: ".strlen(ltrim($data)));
		header("Content-Disposition: attachment; filename=report.pdf");
		echo $data;
		die;
    }

    public function report() {

        $id = JFactory::getApplication()->input->get('id', '0');

        $view = $this->getView('report', 'html');
        $view->report = $this->getReport( $id );

        $view->display();
		
    }

	function getReport($id, $getUserInfo = false)
	{
		$model = $this->getModel();

		$report = array( 'questions' => array() );

		$report['start_data'] = $model->getStartData($id);
		$report['survey_info'] = $model->getSurveyData($report['start_data']->survey_id);

		$questions_data = $model->getQuestionsData($report['start_data']->survey_id);

		if (is_array($questions_data) && count($questions_data) > 0)
			foreach ($questions_data as $k=>$question) {
				if ($question != null)
				{
					$question->sf_qtext = trim(strip_tags(@$question->sf_qtext, '<a><b><i><u>'));
					array_push($report['questions'], array('question' => $question, 'answer_data' => $model->getQuestionHTML($question, $report['start_data']) ));
				}
			}

		if ( $getUserInfo )
		{
			switch ($report['start_data']->usertype)
			{
				case 1;
					$db = JFactory::getDbo();
					$db->setQuery("SELECT * FROM #__users WHERE id = ".$report['start_data']->user_id);
					$report['user_data'] = $db->loadObject();
				break;
				case 2:
					$db = JFactory::getDbo();
					$db->setQuery("SELECT * FROM #__survey_force_users WHERE id = ".$report['start_data']->invite_id);
					$report['user_data'] = $db->loadObject();
				break;
				default:

			}

		}

		return $report;
	}

}
