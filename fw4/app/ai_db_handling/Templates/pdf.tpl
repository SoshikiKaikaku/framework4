pagesize:{$ai_setting.pdf_pagesize}
orientation:{$ai_setting.pdf_orientation}
page_margin_top:{$ai_setting.page_margin_top}
page_margin_left:{$ai_setting.page_margin_left}
page_margin_right:{$ai_setting.page_margin_right}
page_margin_bottom:{$ai_setting.page_margin_bottom}
pagenumber:{$ai_setting.pagenumber}
pagenumber_firstpage:{$ai_setting.pagenumber_firstpage}
img_grayscale:{$ai_setting.img_grayscale}

{foreach $list as $list_data}
{foreach $setting_parameters as $param}
{if $param['para_type'] == 1}
--- fontsize:{$param['fontsize']} {$param['align']} margintop:{$param['margintop']} marginbottom:{$param['marginbottom']} marginleft:{$param['marginleft']} marginright:{$param['marginright']} lineheight:{$param['lineheight']} rotate:{$param['rotate']} border:{$param['border']} 
{$param['text']}	
{/if}
{if $param['para_type'] == 0}
--- fontsize:{$param['fontsize']} {$param['align']} margintop:{$param['margintop']} marginbottom:{$param['marginbottom']} marginleft:{$param['marginleft']} marginright:{$param['marginright']} lineheight:{$param['lineheight']} rotate:{$param['rotate']} border:{$param['border']} 
{if $param['type']=='image' && $list_data[$param['parameter_name']]}
---I file:{$list_data[$param['parameter_name']]} width:{$param['img_width']} height:{$param['img_height']}
{else} 
{$param['parameter_title']}:{$list_data[$param['parameter_name']]}
{/if}
{/if}

{if $param['para_type'] == 2}
--- H separator:| columnsize:{$param['sub_tb']['col_size']} columnalign:{$param['sub_tb']['col_align']} lineheight:4 marginright:0 marginleft:0
{$param['sub_tb']['sub_fields']}
{foreach $param['sub_tb']['sub_values'] as $val}
{$val}		
{/foreach}
{/if}

{/foreach}
--- newpage
{/foreach}
