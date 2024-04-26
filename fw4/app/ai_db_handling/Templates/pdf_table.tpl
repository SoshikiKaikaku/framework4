pagesize:{$ai_setting.pdf_pagesize}
orientation:{$ai_setting.pdf_orientation}
page_margin_top:{$ai_setting.page_margin_top}
page_margin_left:{$ai_setting.page_margin_left}
page_margin_right:{$ai_setting.page_margin_right}
page_margin_bottom:{$ai_setting.page_margin_bottom}
pagenumber:{$ai_setting.pagenumber}
pagenumber_firstpage:{$ai_setting.pagenumber_firstpage}
img_grayscale:{$ai_setting.img_grayscale}


--- H separator:| columnsize:{$col_size} columnalign:{$col_align} lineheight:4 marginright:0 marginleft:0
{$field_str}
{foreach $val_arr as $val}
{$val}
{/foreach}
