<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pdfmaker_class
 *
 * @author nakama
 */
include(dirname(__FILE__) . "/tfpdf/customizedpdf.php");

class pdfmaker_class {

	function makepdf($text, $imgdir, $pdf_filename, $dest = "I") {

		$parameter = array();
		$body = array();
		$default = array();

		// 一旦tempファイルに落とす
		$temp = tmpfile();
		fwrite($temp, $text);
		fseek($temp, 0);

		$c = 0;
		$textname = "";
		while ($line = fgets($temp)) {
			$line = rtrim($line);
			if ($c == 0 && substr($line, 0, 2) == "//") {
				continue;
			}
			if (substr($line, 0, 3) == "---") {
				$paramline = substr($line, 3);
				$param = $this->parseParameterLine($paramline);
				$c++;
				$parameter[$c] = $param;

				if ($param["align"] == "I") {
					$c++;
					$parameter[$c] = array();
				}
			} else {
				// 本文
				$body[$c] .= $line . "\n";
			}
		}

		//ヘッダ部の解析
		$header = array();
		$headertxt = $body[0];
		$headerarr = explode("\n", $headertxt);
		foreach ($headerarr as $headerline) {
			$commentpoint = strpos($headerline, "//");
			if ($commentpoint > 0) {
				$headerline = substr($headerline, 0, $commentpoint);
				$headerline = str_replace(" ", "", $headerline);
			}
			if ($headerline != "") {
				if (substr($headerline, 0, 1) == "#") {
					$exp = explode(" ", $headerline, 2);
					$param = $this->parseParameterLine($exp[1]);
					$header[$exp[0]] = $param;
				} else {
					$set = explode(":", $headerline, 2);
					$header[$set[0]] = $set[1];
				}
			}
		}

		fclose($temp);

//		echo "<pre>";
//		var_dump($body);
//		echo "</pre>";
		$this->makepdfpage($header, $parameter, $body, $imgdir, $pdf_filename, $dest);
	}

	function makepdfpage($header, $parameter, $body, $imgdir, $pdf_filename, $dest = "I") {

		// -----------
		// ヘッダ処理とデフォルト設定
		// -----------
		$default = array();
		if (isset($header["orientation"])) {
			$orientation = $header["orientation"];
		} else {
			$orientation = "P";
		}
		if (isset($header["pagesize"])) {
			$pagesize = $header["pagesize"];
		} else {
			$pagesize = "A4";
		}
		if (isset($header["font"])) {
			$font = $header["font"];
		} else {
			$font = "gothic";
		}
		if (isset($header["page_margin_top"])) {
			$tMargin = $header["page_margin_top"];
		} else {
			$tMargin = 20;
		}
		if (isset($header["page_margin_left"])) {
			$lMargin = $header["page_margin_left"];
		} else {
			$lMargin = 30;
		}
		if (isset($header["page_margin_right"])) {
			$rMargin = $header["page_margin_right"];
		} else {
			$rMargin = 30;
		}
		if (isset($header["page_margin_bottom"])) {
			$bMargin = $header["page_margin_bottom"];
		} else {
			$bMargin = 30;
		}
		if (isset($header["pagenumber"])) {
			$pagenumberflg = $header["pagenumber"];
		} else {
			$pagenumberflg = "on";
		}
		if (isset($header["margintop"])) {
			$default["margintop"] = $header["margintop"];
		} else {
			$default["margintop"] = 0;
		}
		if (isset($header["pagenumber_firstpage"])) {
			$pagenumber_firstpage = $header["pagenumber_firstpage"];
		} else {
			$pagenumber_firstpage = "on";
		}
		if (isset($header["publish"])) {
			$publish = $header["publish"];
		} else {
			$publish = "off";
		}

		if (isset($header["img_grayscale"])) {
			$img_grayscale = $header["img_grayscale"];
		} else {
			$img_grayscale = "off";
		}

		if (isset($header["pagenumber_y_position"])) {
			$pagenumber_y_position = $header["pagenumber_y_position"];
		} else {
			$pagenumber_y_position = 0;
		}


		// PDFの生成開始
		$pdf = new CustomizedPDF($orientation, "mm", $pagesize);

		// 画像ディレクトリセット
		$pdf->imgdir = $imgdir;

		// フッター非表示
		if ($pagenumberflg != "on") {
			$pdf->hidefooter();
		}

		// ページ番号の位置調整
		$pdf->pagenumber_y_position($pagenumber_y_position);

		if ($pagenumber_firstpage != "on") {
			$pdf->pagenumber_firstpage_off();
		}

		//フォント設定
		$pdf->AddFont("mincho", '', 'ipam.ttf', true);
		$pdf->AddFont("Pmincho", '', 'ipamp.ttf', true);
		$pdf->AddFont("EXmincho", '', 'ipaexm.ttf', true);

		$pdf->AddFont("Pgothic", '', 'ipagp.ttf', true);
		$pdf->AddFont("gothic", '', 'ipag.ttf', true);
		$pdf->AddFont("EXgothic", '', 'ipaexg.ttf', true);

		$pdf->AddFont("migmix-1p-bold", '', 'migmix-1p-bold.ttf', true);
		$pdf->AddFont("migmix-1p-regular", '', 'migmix-1p-regular.ttf', true);
		$pdf->AddFont("gnu", "", "gnu_unifont.ttf", true);
//		$pdf->AddFont("cjk","","NotoSansCJKjp-Regular.otf",true);
		$default["fontname"] = $font;
		$pdf->setPageNumberFont($font);

		// ページ作成
		$pdf->AddPage();
		$pdf->resetPageTotal();
		$pdf->AliasNbPages(); // 連番を付ける
		// マージンの設定
		$default["fontsize"] = 10;
		$default["lineheight"] = 4;
		$wPage = $pdf->GetPageWidth() - $lMargin - $rMargin;  // 書ける領域の幅
		$default["wPage"] = $wPage;
		$pdf->SetMargins($lMargin, $tMargin, $rMargin);
		$pdf->SetX($lMargin);
		$pdf->SetY($tMargin);
		$pdf->SetAutoPageBreak(true, $bMargin);

		//--------------
		// 入力データ処理
		//--------------
		for ($c = 1; $c <= count($parameter); $c++) {

			// パラメーターをセット
			$set = $this->setParameter($pdf, $parameter[$c], $default);

			// フォーム除外
			if ($set["print"] == "false") {
				continue;
			}

			//-----------------
			// 描画
			//-----------------
			if ($set["align"] == "H") {
				//---------
				// 表
				//---------
				// セパレーター
				if (isset($set["separator"])) {
					$separator = str_replace("\"", "", $set["separator"]);
				} else {
					$separator = "/";
				}

				// 表のデータの処理
				$datatxt = $body[$c];
				$lines = explode("\n", $datatxt);
				$datalist = array();
				foreach ($lines as $line) {
					if ($line != "") {
						$dataset = array();
						$subitems = explode($separator, $line);
						$blankflg = true;
						foreach ($subitems as $item) {
							$blankcheck = $item;
							$blankcheck = str_replace(array('\n', '　'), "", $blankcheck);
							if (!empty($blankcheck)) {
								$blankflg = false;
							}
							$item = str_replace('\n', "\n", $item);
							$dataset[] = $item;
						}
						if (isset($set["delete_blank_line"]) && $blankflg) {
							// データを入れない
						} else {
							$datalist[] = $dataset;
						}
					}
				}


				// カラムの横幅を指定
				$coltxt = $set["columnsize"];
				if (!empty($coltxt)) {
					// 横幅をカスタマイズ
					$cols = explode(",", $coltxt);
					$pdf->tablewidths = array();
					foreach ($cols as $colsize) {
						$pdf->tablewidths[] = ($wPage - $set["marginright"] - $set["marginleft"]) * ($colsize / 100);
					}
				} else {
					$maxlen = array();
					foreach ($datalist as $cols) {
						foreach ($cols as $key => $col) {
							$txts = explode('\n', $col);
							foreach ($txts as $txt) {
								if ($maxlen[$key] < strlen($txt)) {
									$maxlen[$key] = strlen($txt);
								}
							}
						}
					}

					$sum = 0;
					$avglen = array();
					//合計を計算
					foreach ($maxlen as $key => $val) {
						$sum += $val;
					}
					// 各カラムサイズを設定
					$pdf->tablewidths = array();
					foreach ($maxlen as $key => $val) {
						$colper = $val / $sum;
						$pdf->tablewidths[] = ($wPage - $set["marginright"] - $set["marginleft"]) * $colper;
					}
				}

				// カラム毎の右寄せ・左寄せの指定
				$aligntxt = $set["columnalign"];
				$aligns = explode(",", $aligntxt);
				$pdf->tablealigns = $aligns;

				// 表示
				$pdf->morepagestable($datalist, $set["lineheight"], $set["marginleft"], $set["fillflg"], $set);
			} else if ($set["align"] == "I") {
				//-------------
				// 画像
				//-------------

				if (!is_array($imgdir)) {
					$imgdir = [$imgdir];
				}

				foreach ($imgdir as $idir) {
					$img = $idir . $set["file"];
					if (file_exists($img)) {

						if (empty($set["x"])) {
							$x = null;
						} else {
							$x = $set["x"];
						}

						if (empty($set["y"])) {
							$y = null;
						} else {
							$y = $set["y"];
						}

						if ($set["width"] > 0 && $set["height"] > 0) {
							$original = getimagesize($img);
							$original_w = $original[0];
							$original_h = $original[1];

							if ($original_w > 0) {
								$is_h_auto = ($set["width"] / $original_w) * $original_h;

								if ($is_h_auto < $set["height"]) {
									$w = $set["width"];
									$h = 'auto';
								} else {
									$w = 'auto';
									$h = $set["height"];
								}
							}else{
								// オリジナルの画像サイズが取得できない場合がある
								$w = $set["width"];
								$h = 'auto';
							}
						} elseif ($set["width"] > 0) {
							$w = $set["width"];
							$h = 'auto';
						} elseif ($set["height"] > 0) {
							$w = 'auto';
							$h = $set["height"];
						} else {
							$w = 'auto';
							$h = 'auto';
						}

//						if($set["width"]>0){
//							$w = $set["width"];
//						}else{
//							$w = 0;
//						}
//						if($set["height"]>0){
//							$h = $set["height"];
//						}else{
//							$h = 0;
//						}

						if ($img_grayscale == "on") {
							$newfile = $img . "-grayscale.jpg";
							image_grayscale($img, $newfile);
							$pdf->Image($newfile, $x, $y, $w, $h);
						} else {
							$pdf->Image($img, $x, $y, $w, $h);
						}

						break;
					}
				}
			} else if ($set["align"] == "RECT") {
				//----------
				// 四角
				//----------
				$x = $set["x"];
				$y = $set["y"];
				$w = $set["w"];
				$h = $set["h"];
				$rectstyle = "D";
				if (isset($set["background"])) {
					$rectstyle .= "F";
				}
				$pdf->Rect($x, $y, $w, $h, $rectstyle);
			} else if ($set["align"] == "HR") {
				//----------
				// 横ライン
				//----------
				$x1 = $lMargin;
				$y1 = $pdf->GetY();
				$x2 = $pdf->GetPageWidth() - $rMargin;
				$y2 = $y1;
				if (isset($set["marginleft"])) {
					$x1 += $set["marginleft"];
				}
				if (isset($set["marginright"])) {
					$x2 -= $set["marginright"];
				}
				$pdf->Line($x1, $y1, $x2, $y2);
			} else {
				//-----------
				// テキスト
				//-----------
				$item = $body[$c];
				$item = str_replace('\n', "\n", $item);

				// X,Y指定
				if (isset($set["x"])) {
					if (substr($set["x"], 0, 1) == "-") {
						$v = substr($set["x"], 1);
						$pdf->SetX($pdf->GetX() - $v);
					} else if (substr($set["x"], 0, 1) == "+") {
						$v = substr($set["x"], 1);
						$pdf->SetX($pdf->GetX() + $v);
					} else {
						$pdf->SetX($set["x"]);
					}
				}
				if (isset($set["y"])) {
					if (substr($set["y"], 0, 1) == "-") {
						$v = substr($set["y"], 1);
						$pdf->SetY($pdf->GetY() - $v, false);
					} else if (substr($set["y"], 0, 1) == "+") {
						$v = substr($set["y"], 1);
						$pdf->SetY($pdf->GetT() + $v, false);
					} else {
						$pdf->SetY($set["y"], false);
					}
				}

				// Rotate
				if ($set["rotate"] != 0) {
					$pdf->Rotate($set["rotate"]);
				}

				$lines = explode("\n", $item);
				$txt = "";

				foreach ($lines as $line) {
					//---------
					// H1〜H6見出し
					//---------
					if (substr($line, 0, 1) == "#") {
						if (!empty($txt)) {
							// 見出しより前のテキストを書き出し
							$pdf->MultiCell($set["width"], $set["lineheight"], $txt, $set["border"], $set["align"], $set["fillflg"]);
							$txt = "";
						}

						$ex = explode(" ", $line, 2);
						$h = $ex[0];
						$htxt = $ex[1];
						$parameter_h = $header[$h];
						$set_h = $this->setParameter($pdf, $parameter_h, $default);
						$htxt = $set_h["before"] . $htxt . $set_h["after"];
						$pdf->MultiCell($set_h["width"], $set_h["lineheight"], $htxt, $set_h["border"], $set_h["align"], $set_h["fillflg"]);
						$pdf->setY($pdf->getY() + $set_h["marginbottom"] - $pdf->cMargin);
						$tmp_parameter = $parameter[$c];
						$tmp_parameter["margintop"] = 0; //margintopキャンセル
						unset($tmp_parameter["newpage"]); // newpageもキャンセル
						$set = $this->setParameter($pdf, $tmp_parameter, $default); //戻す
					} else {
						$txt .= $line . "\n";
					}
				}
				if (!empty($txt)) {
					$pdf->MultiCell($set["width"], $set["lineheight"], $txt, $set["border"], $set["align"], $set["fillflg"]);
				}

				// Rotateを戻す
				if ($set["rotate"] != 0) {
					$pdf->Rotate(0);
				}
			}
		}

		// 2の倍数のページ数にする
		if ($publish == "on") {
			$pn = $pdf->PageNo();
			while (($pn % 2) != 0) {
				$pdf->AddPage();
				$pn = $pdf->PageNo();
			}
		}

		return $pdf->Output($pdf_filename, $dest);
	}

	function setParameter($pdf, $parameter, $default) {
		// 設定を取得
		$set = $parameter;

		//------------
		// ページ替え
		//------------
		if (isset($set["newpage"])) {
			$pdf->AddPage();
		}

		//------------
		// フォントの設定
		//------------
		// fontsize
		if (!isset($set["fontsize"])) {
			$set["fontsize"] = $default["fontsize"];
		}
		// underline
		$style = "";
		if (isset($set["underline"])) {
			$style .= "U";
		}
		if (isset($set["bold"])) {
			$style .= "B";
		}
		$set["style"] = $style;
		// color
		if (isset($set["color"])) {
			$colortxt = $set["color"];
			$colorarr = explode(",", $colortxt);
			$pdf->SetTextColor($colorarr[0], $colorarr[1], $colorarr[2]);
		} else {
			$pdf->SetTextColor(0, 0, 0);
		}
		// fill
		if (isset($set["fill"]) || isset($set["background"])) {
			if (isset($set["fill"])) {
				$colortxt = $set["fill"];
			} else {
				$colortxt = $set["background"];
			}
			$colorarr = explode(",", $colortxt);
			$pdf->SetFillColor($colorarr[0], $colorarr[1], $colorarr[2]);
			$set["fillflg"] = true;
		} else {
			$set["fillflg"] = false;
		}
		// linecolor
		if (isset($set["linecolor"])) {
			$colortxt = $set["linecolor"];
			$colorarr = explode(",", $colortxt);
			$pdf->SetDrawColor($colorarr[0], $colorarr[1], $colorarr[2]);
		} else {
			$pdf->SetDrawColor(0, 0, 0);
		}
		// linewidth
		if (isset($set["linewidth"])) {
			$linewidth = $set["linewidth"];
			$pdf->SetLineWidth($linewidth);
		} else {
			$pdf->SetLineWidth(0.2);
		}
		// fontname
		if (!isset($set["fontname"])) {
			$set["fontname"] = $default["fontname"];
		}

		// フォントの設定
		$pdf->SetFont($set["fontname"], $set["style"], $set["fontsize"]);

		//------------
		// 描画位置の設定
		//------------
		// margin
		if (isset($set["margintop"])) {
			$pdf->SetY($pdf->GetY() + $set["margintop"]);
		} else {
			$pdf->SetY($pdf->GetY() + $default["margintop"]);
		}
		if (isset($set["marginleft"])) {
			$pdf->SetX($pdf->GetX() + $set["marginleft"]);
		} else {
			$set["marginleft"] = 0;
		}
		if (!isset($set["width"])) {
			if (isset($set["marginright"])) {
				$set["width"] = $default["wPage"] - $set["marginleft"] - $set["marginright"];
			} else {
				$set["width"] = 0;
			}
		}
		if (!isset($set["marginright"])) {
			$set["marginright"] = 0;
		}
		if (!isset($set["marginbottom"])) {
			$set["marginbottom"] = 0;
		}

		//------------
		// 装飾の設定
		//------------
		// lineheight
		if (!isset($set["lineheight"])) {
			$set["lineheight"] = $default["lineheight"];
		}

		// border
		if (!isset($set["border"])) {
			$set["border"] = 0;
		}

		// Rotate
		if (!isset($set["rotate"])) {
			$set["rotate"] = 0;
		}

		// table border
		if (!isset($set["table_border"])) {
			$set["table_border"] = "VH";
		}

		if (isset($set["span_x"]) || isset($set["span_y"])) {
			// span が設定してあると必ずrowbreakにする
			$set["rowbreak"] = true;
		}

		if (!isset($set["span_x"])) {
			$set["span_x"] = 0;
		}
		if (!isset($set["span_y"])) {
			$set["span_y"] = 0;
		}

		return $set;
	}

	function parseParameterLine($paramline) {
		$paramarr = explode(" ", $paramline);
		$param = array();
		foreach ($paramarr as $paramtxt) {
			if ($paramtxt == "R" || $paramtxt == "L" || $paramtxt == "C" || $paramtxt == "H" || $paramtxt == "I" || $paramtxt == "RECT" || $paramtxt == "HR") {
				$param["align"] = $paramtxt;
			} else {
				if ($paramtxt != "") {
					if (strpos($paramtxt, ":") > 0) {
						$set = explode(":", $paramtxt, 2);
						$set[0] = str_replace("-", "", $set[0]);
						$param[$set[0]] = $set[1];
					} else {
						$param[$paramtxt] = "1";
					}
				}
			}
		}
		return $param;
	}
}
