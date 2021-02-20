<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * <strong style="color:#000000;">故梦第三方登陆用户版</strong>
 * 
 * @package GmOauth
 * @author Gm
 * @version 2.0
 * @update: 2021-1-31
 * @link //www.gmit.vip
 */
class GmOauth_Plugin implements Typecho_Plugin_Interface
{
    public static $panel = 'GmOauth/console.php';
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Helper::addPanel(1, self::$panel, _t('快捷登录绑定'), _t('快捷登录绑定设置'), 'subscriber');
        Helper::addRoute('GmOauth', '/GmOauth/', 'GmOauth_Action', 'GmOauth');
        Helper::addRoute('GmOauthCallback', '/GmOauth/Callback', 'GmOauth_Action', 'GmOauthCallback');
        Helper::addRoute('GmOauthBind', '/GmOauth/Bind', 'GmOauth_Action', 'GmOauthBind');
        try {
            $db = Typecho_Db::get();
            $prefix = $db->getPrefix();
            $sql = "CREATE TABLE IF NOT EXISTS `typecho_gm_oauth` (
  `id` int(255) NOT NULL,
  `app` text NOT NULL,
  `uid` int(255) NOT NULL,
  `openid` text NOT NULL,
  `time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `typecho_gm_oauth`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `typecho_gm_oauth`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;";
            $db->query($sql);
            return '插件安装成功!数据库安装成功';
        } catch (Typecho_Db_Exception $e) {
            if ('42S01' == $e->getCode()) {
                return '插件安装成功!数据库已存在!';
            }
        }
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){
        Helper::removePanel(1, self::$panel);
        Helper::removeRoute('GmOauth');
        Helper::removeRoute('GmOauthCallback');
        Helper::removeRoute('GmOauthBind');
        return '插件卸载成功';
    }
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
     
    /**
     *为header添加css文件
     * @return void
     */
    /*public static function header()
    {
        
    }*/
    
        /**
     *为footer添加js文件
     * @return void
     */
    /*public static function footer(){
        
    }*/
    
    public static function GmOauth()
    {
        echo '<div class="row text-center" style="margin-top:-5px;">
                <a href="//'.$_SERVER['HTTP_HOST'].'/GmOauth?site=Dingtalk" class="btn btn-rounded btn-sm btn-icon btn-default" data-toggle="tooltip" data-placement="bottom" data-original-title="钉钉账号登陆"><svg t="1607250725796" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4008" width="25" height="25" style="margin: 1px 1px 1px 2px;"><path d="M512.003 79C272.855 79 79 272.855 79 512.003 79 751.145 272.855 945 512.003 945 751.145 945 945 751.145 945 512.003 945 272.855 751.145 79 512.003 79z m200.075 375.014c-0.867 3.764-3.117 9.347-6.234 16.012h0.087l-0.347 0.648c-18.183 38.86-65.631 115.108-65.631 115.108l-0.215-0.52-13.856 24.147h66.8L565.063 779l29.002-115.368h-52.598l18.27-76.29c-14.76 3.55-32.253 8.436-52.945 15.1 0 0-27.967 16.36-80.607-31.5 0 0-35.501-31.29-14.891-39.078 8.744-3.33 42.466-7.573 69.004-11.122 35.93-4.845 57.965-7.441 57.965-7.441s-110.607 1.643-136.841-2.468c-26.237-4.11-59.525-47.905-66.626-86.377 0 0-10.953-21.117 23.595-11.122 34.547 10 177.535 38.95 177.535 38.95s-185.933-56.992-198.36-70.929c-12.381-13.846-36.406-75.902-33.289-113.981 0 0 1.343-9.521 11.127-6.926 0 0 137.49 62.75 231.475 97.152 94.028 34.403 175.76 51.885 165.2 96.414z" fill="#3AA2EB" p-id="4009"></path></svg></a>
                <a href="//'.$_SERVER['HTTP_HOST'].'/GmOauth?site=Tencent" class="btn btn-rounded btn-sm btn-icon btn-default" data-toggle="tooltip" data-placement="bottom" data-original-title="QQ快捷登陆"><svg t="1607251153785" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="6106" width="25" height="25"><path d="M511.09761 957.257c-80.159 0-153.737-25.019-201.11-62.386-24.057 6.702-54.831 17.489-74.252 30.864-16.617 11.439-14.546 23.106-11.55 27.816 13.15 20.689 225.583 13.211 286.912 6.767v-3.061z" fill="#FAAD08" p-id="6107"></path><path d="M496.65061 957.257c80.157 0 153.737-25.019 201.11-62.386 24.057 6.702 54.83 17.489 74.253 30.864 16.616 11.439 14.543 23.106 11.55 27.816-13.15 20.689-225.584 13.211-286.914 6.767v-3.061z" fill="#FAAD08" p-id="6108"></path><path d="M497.12861 474.524c131.934-0.876 237.669-25.783 273.497-35.34 8.541-2.28 13.11-6.364 13.11-6.364 0.03-1.172 0.542-20.952 0.542-31.155C784.27761 229.833 701.12561 57.173 496.64061 57.162 292.15661 57.173 209.00061 229.832 209.00061 401.665c0 10.203 0.516 29.983 0.547 31.155 0 0 3.717 3.821 10.529 5.67 33.078 8.98 140.803 35.139 276.08 36.034h0.972z" fill="#000000" p-id="6109"></path><path d="M860.28261 619.782c-8.12-26.086-19.204-56.506-30.427-85.72 0 0-6.456-0.795-9.718 0.148-100.71 29.205-222.773 47.818-315.792 46.695h-0.962C410.88561 582.017 289.65061 563.617 189.27961 534.698 185.44461 533.595 177.87261 534.063 177.87261 534.063 166.64961 563.276 155.56661 593.696 147.44761 619.782 108.72961 744.168 121.27261 795.644 130.82461 796.798c20.496 2.474 79.78-93.637 79.78-93.637 0 97.66 88.324 247.617 290.576 248.996a718.01 718.01 0 0 1 5.367 0C708.80161 950.778 797.12261 800.822 797.12261 703.162c0 0 59.284 96.111 79.783 93.637 9.55-1.154 22.093-52.63-16.623-177.017" fill="#000000" p-id="6110"></path><path d="M434.38261 316.917c-27.9 1.24-51.745-30.106-53.24-69.956-1.518-39.877 19.858-73.207 47.764-74.454 27.875-1.224 51.703 30.109 53.218 69.974 1.527 39.877-19.853 73.2-47.742 74.436m206.67-69.956c-1.494 39.85-25.34 71.194-53.24 69.956-27.888-1.238-49.269-34.559-47.742-74.435 1.513-39.868 25.341-71.201 53.216-69.974 27.909 1.247 49.285 34.576 47.767 74.453" fill="#FFFFFF" p-id="6111"></path><path d="M683.94261 368.627c-7.323-17.609-81.062-37.227-172.353-37.227h-0.98c-91.29 0-165.031 19.618-172.352 37.227a6.244 6.244 0 0 0-0.535 2.505c0 1.269 0.393 2.414 1.006 3.386 6.168 9.765 88.054 58.018 171.882 58.018h0.98c83.827 0 165.71-48.25 171.881-58.016a6.352 6.352 0 0 0 1.002-3.395c0-0.897-0.2-1.736-0.531-2.498" fill="#FAAD08" p-id="6112"></path><path d="M467.63161 256.377c1.26 15.886-7.377 30-19.266 31.542-11.907 1.544-22.569-10.083-23.836-25.978-1.243-15.895 7.381-30.008 19.25-31.538 11.927-1.549 22.607 10.088 23.852 25.974m73.097 7.935c2.533-4.118 19.827-25.77 55.62-17.886 9.401 2.07 13.75 5.116 14.668 6.316 1.355 1.77 1.726 4.29 0.352 7.684-2.722 6.725-8.338 6.542-11.454 5.226-2.01-0.85-26.94-15.889-49.905 6.553-1.579 1.545-4.405 2.074-7.085 0.242-2.678-1.834-3.786-5.553-2.196-8.135" fill="#000000" p-id="6113"></path><path d="M504.33261 584.495h-0.967c-63.568 0.752-140.646-7.504-215.286-21.92-6.391 36.262-10.25 81.838-6.936 136.196 8.37 137.384 91.62 223.736 220.118 224.996H506.48461c128.498-1.26 211.748-87.612 220.12-224.996 3.314-54.362-0.547-99.938-6.94-136.203-74.654 14.423-151.745 22.684-215.332 21.927" fill="#FFFFFF" p-id="6114"></path><path d="M323.27461 577.016v137.468s64.957 12.705 130.031 3.91V591.59c-41.225-2.262-85.688-7.304-130.031-14.574" fill="#EB1C26" p-id="6115"></path><path d="M788.09761 432.536s-121.98 40.387-283.743 41.539h-0.962c-161.497-1.147-283.328-41.401-283.744-41.539l-40.854 106.952c102.186 32.31 228.837 53.135 324.598 51.926l0.96-0.002c95.768 1.216 222.4-19.61 324.6-51.924l-40.855-106.952z" fill="#EB1C26" p-id="6116"></path></svg></a>
                <a href="//'.$_SERVER['HTTP_HOST'].'/GmOauth?site=Baidu" class="btn btn-rounded btn-sm btn-icon btn-default" data-toggle="tooltip" data-placement="bottom" data-original-title="百度账号登陆"><svg t="1607251207708" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="7132" width="25" height="25"><path d="M345.706 297.423c15.35 12.792 35.817 20.467 53.726 17.908 20.467 0 38.375-10.233 53.725-23.025 17.91-15.35 30.7-35.817 40.934-58.842C512 189.97 512 136.245 496.65 90.194c-10.234-30.7-28.142-58.842-53.726-76.75C427.574 3.21 404.548-1.906 384.081 0.652c-12.791 2.558-25.583 7.675-38.375 15.35-23.025 15.35-38.376 40.934-48.61 66.518-12.791 46.05-15.35 92.101-2.557 138.152 10.233 28.142 25.583 56.284 51.167 76.75z m255.837 2.558c17.909 15.35 40.934 25.584 63.96 25.584 20.466 2.558 38.375-2.558 56.283-12.792 17.909-10.233 33.26-25.584 43.493-43.492 12.792-20.467 23.025-40.934 30.7-63.96 5.117-17.908 7.675-38.375 5.117-58.842-2.559-28.142-15.35-53.726-33.259-76.751-12.792-15.35-28.142-30.7-46.05-38.376-12.792-5.116-28.143-10.233-40.935-7.675-17.908 2.559-33.258 12.792-46.05 23.026-17.909 15.35-33.259 33.258-43.493 53.725-10.233 17.909-20.466 38.376-23.025 61.401-2.558 25.584-2.558 51.168 2.559 74.193 5.116 23.025 12.791 46.05 30.7 63.96zM245.929 509.768c17.91-15.35 28.143-35.818 35.818-56.285 10.233-33.258 10.233-66.517 7.675-99.776 0-12.792-5.117-25.584-10.234-38.376-12.792-28.142-35.817-53.725-63.959-69.076-23.025-10.233-46.05-15.35-66.518-10.233-25.583 2.558-46.05 20.467-61.4 40.934-20.467 28.142-30.7 63.96-35.818 97.218-2.558 20.467 0 40.934 5.117 61.4 7.675 30.701 23.025 58.843 46.05 79.31 17.91 15.35 40.935 23.026 63.96 23.026 28.142 0 56.284-7.675 79.31-28.142z m736.811-76.752c-2.558-20.467-7.675-38.375-17.908-56.284-10.234-20.467-28.143-40.934-48.61-51.167-23.025-12.792-51.167-15.35-76.75-12.792-12.792 2.558-28.143 5.117-40.935 12.792-17.908 10.233-30.7 28.142-40.933 48.609-10.234 25.584-15.35 53.726-15.35 81.868 0 25.583 0 53.726 7.674 79.31 5.117 17.908 15.35 38.375 33.26 48.608 17.908 15.35 40.933 20.467 63.959 23.026 17.908 2.558 38.375 2.558 56.284-2.559 17.908-5.116 35.817-15.35 46.05-30.7 12.792-15.35 20.467-35.817 23.026-53.726 12.792-30.7 10.233-58.842 10.233-86.985zM911.106 819.33c-2.559-35.817-20.467-71.634-46.05-99.776-5.118-5.117-10.234-10.234-17.91-15.35-33.258-28.142-66.517-58.843-99.776-89.543-33.259-33.26-63.96-69.076-92.101-107.452-20.467-33.259-48.61-63.96-86.985-81.868-23.025-10.233-51.167-15.35-76.751-12.792-46.05 5.117-86.985 30.7-115.127 66.518-7.675 7.675-12.791 17.909-17.908 28.142-20.467 30.7-46.05 61.401-74.193 86.985-15.35 15.35-30.7 28.142-46.05 40.934-7.676 7.675-17.91 15.35-25.584 23.025-30.7 23.025-61.401 53.726-79.31 86.985-12.792 23.025-20.467 48.609-23.025 76.75 0 23.026 2.558 46.051 10.233 66.518 7.675 23.026 17.909 46.051 33.26 63.96 25.583 30.7 63.958 51.167 102.334 53.725 48.609 2.559 97.218 0 143.269-7.675 20.467-2.558 40.934-10.233 63.959-12.792 46.05-5.116 92.101-2.558 135.594 10.234 35.817 12.792 74.192 17.909 112.568 20.467 38.375 2.558 79.31-2.558 115.127-23.025 25.583-12.792 46.05-35.818 58.842-61.401 20.467-33.26 30.7-71.635 25.584-112.569zM481.3 924.224H363.615c-12.792 0-25.584 0-38.376-2.559-25.584-5.117-48.61-20.467-63.96-43.492-12.791-15.35-20.466-33.259-23.025-53.726a246.563 246.563 0 0 1 0-61.4c5.117-23.026 17.909-43.493 33.26-58.843 12.791-12.792 30.7-23.026 48.608-30.7 7.675-2.56 15.35-5.117 23.026-5.117h69.076v-97.219h66.517c2.559 120.244 2.559 237.929 2.559 353.056z m263.512 0H583.634c-17.908-2.559-33.258-7.676-46.05-17.909-15.35-12.792-23.026-33.259-23.026-51.167v-173.97h66.518v161.178c0 7.675 2.558 12.792 7.675 17.908 5.117 5.117 12.792 7.675 20.467 7.675h69.076V678.62h66.518v245.604z" fill="#306CFF" p-id="7133"></path><path d="M340.59 734.904c-12.793 5.117-25.585 15.35-33.26 30.7-5.116 12.792-7.675 25.584-7.675 38.376 0 15.35 5.117 30.7 12.792 43.492 10.234 15.35 28.142 25.584 46.05 23.026h53.727V732.346H353.38c-2.558-2.559-7.675 0-12.792 2.558z" fill="#306CFF" p-id="7134"></path></svg></a>
                <a href="//'.$_SERVER['HTTP_HOST'].'/GmOauth?site=Gitee" class="btn btn-rounded btn-sm btn-icon btn-default" data-toggle="tooltip" data-placement="bottom" data-original-title="Gitee码云登陆"><svg t="1607251234258" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="7355" width="25" height="25"><path d="M512 1024C230.4 1024 0 793.6 0 512S230.4 0 512 0s512 230.4 512 512-230.4 512-512 512z m259.2-569.6H480c-12.8 0-25.6 12.8-25.6 25.6v64c0 12.8 12.8 25.6 25.6 25.6h176c12.8 0 25.6 12.8 25.6 25.6v12.8c0 41.6-35.2 76.8-76.8 76.8h-240c-12.8 0-25.6-12.8-25.6-25.6V416c0-41.6 35.2-76.8 76.8-76.8h355.2c12.8 0 25.6-12.8 25.6-25.6v-64c0-12.8-12.8-25.6-25.6-25.6H416c-105.6 0-188.8 86.4-188.8 188.8V768c0 12.8 12.8 25.6 25.6 25.6h374.4c92.8 0 169.6-76.8 169.6-169.6v-144c0-12.8-12.8-25.6-25.6-25.6z" fill="#d81e06" p-id="7356"></path></svg></a>
                <a href="//'.$_SERVER['HTTP_HOST'].'/GmOauth?site=Github" class="btn btn-rounded btn-sm btn-icon btn-default" data-toggle="tooltip" data-placement="bottom" data-original-title="GitHub登陆"><svg t="1607251256441" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="8216" width="25" height="25"><path d="M512 85.333333C276.266667 85.333333 85.333333 276.266667 85.333333 512a426.410667 426.410667 0 0 0 291.754667 404.821333c21.333333 3.712 29.312-9.088 29.312-20.309333 0-10.112-0.554667-43.690667-0.554667-79.445333-107.178667 19.754667-134.912-26.112-143.445333-50.133334-4.821333-12.288-25.6-50.133333-43.733333-60.288-14.933333-7.978667-36.266667-27.733333-0.554667-28.245333 33.621333-0.554667 57.6 30.933333 65.621333 43.733333 38.4 64.512 99.754667 46.378667 124.245334 35.2 3.754667-27.733333 14.933333-46.378667 27.221333-57.045333-94.933333-10.666667-194.133333-47.488-194.133333-210.688 0-46.421333 16.512-84.778667 43.733333-114.688-4.266667-10.666667-19.2-54.4 4.266667-113.066667 0 0 35.712-11.178667 117.333333 43.776a395.946667 395.946667 0 0 1 106.666667-14.421333c36.266667 0 72.533333 4.778667 106.666666 14.378667 81.578667-55.466667 117.333333-43.690667 117.333334-43.690667 23.466667 58.666667 8.533333 102.4 4.266666 113.066667 27.178667 29.866667 43.733333 67.712 43.733334 114.645333 0 163.754667-99.712 200.021333-194.645334 210.688 15.445333 13.312 28.8 38.912 28.8 78.933333 0 57.045333-0.554667 102.912-0.554666 117.333334 0 11.178667 8.021333 24.490667 29.354666 20.224A427.349333 427.349333 0 0 0 938.666667 512c0-235.733333-190.933333-426.666667-426.666667-426.666667z" fill="#000000" p-id="8217"></path></svg></a>
                <a href="//'.$_SERVER['HTTP_HOST'].'/GmOauth?site=Sina" class="btn btn-rounded btn-sm btn-icon btn-default" data-toggle="tooltip" data-placement="bottom" data-original-title="新浪微博账号登录"><svg t="1607496736049" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4012" width="25" height="25"><path d="M851.4 590.193c-22.196-66.233-90.385-90.422-105.912-91.863-15.523-1.442-29.593-9.94-19.295-27.505 10.302-17.566 29.304-68.684-7.248-104.681-36.564-36.14-116.512-22.462-173.094 0.866-56.434 23.327-53.39 7.055-51.65-8.925 1.89-16.848 32.355-111.02-60.791-122.395C311.395 220.86 154.85 370.754 99.572 457.15 16 587.607 29.208 675.873 29.208 675.873h0.58c10.009 121.819 190.787 218.869 412.328 218.869 190.5 0 350.961-71.853 398.402-169.478 0 0 0.143-0.433 0.575-1.156 4.938-10.506 8.71-21.168 11.035-32.254 6.668-26.205 11.755-64.215-0.728-101.66z m-436.7 251.27c-157.71 0-285.674-84.095-285.674-187.768 0-103.671 127.82-187.76 285.674-187.76 157.705 0 285.673 84.089 285.673 187.76 0 103.815-127.968 187.768-285.673 187.768z" fill="#E71F19" p-id="4013"></path><path d="M803.096 425.327c2.896 1.298 5.945 1.869 8.994 1.869 8.993 0 17.7-5.328 21.323-14.112 5.95-13.964 8.993-28.793 8.993-44.205 0-62.488-51.208-113.321-114.181-113.321-15.379 0-30.32 3.022-44.396 8.926-11.755 4.896-17.263 18.432-12.335 30.24 4.933 11.662 18.572 17.134 30.465 12.238 8.419-3.46 17.268-5.33 26.41-5.33 37.431 0 67.752 30.241 67.752 67.247 0 9.068-1.735 17.857-5.369 26.202a22.832 22.832 0 0 0 12.335 30.236l0.01 0.01z" fill="#F5AA15" p-id="4014"></path><path d="M726.922 114.157c-25.969 0-51.65 3.744-76.315 10.942-18.423 5.472-28.868 24.622-23.5 42.91 5.509 18.29 24.804 28.657 43.237 23.329a201.888 201.888 0 0 1 56.578-8.064c109.253 0 198.189 88.271 198.189 196.696 0 19.436-2.905 38.729-8.419 57.16-5.508 18.289 4.79 37.588 23.212 43.053 3.342 1.014 6.817 1.442 10.159 1.442 14.943 0 28.725-9.648 33.37-24.48 7.547-24.906 11.462-50.826 11.462-77.175-0.143-146.588-120.278-265.813-267.973-265.813z" fill="#F5AA15" p-id="4015"></path><path d="M388.294 534.47c-84.151 0-152.34 59.178-152.34 132.334 0 73.141 68.189 132.328 152.34 132.328 84.148 0 152.337-59.182 152.337-132.328 0-73.15-68.19-132.334-152.337-132.334zM338.53 752.763c-29.454 0-53.39-23.755-53.39-52.987 0-29.228 23.941-52.989 53.39-52.989 29.453 0 53.39 23.76 53.39 52.989 0 29.227-23.937 52.987-53.39 52.987z m99.82-95.465c-6.382 11.086-19.296 15.696-28.726 10.219-9.43-5.323-11.75-18.717-5.37-29.803 6.386-11.09 19.297-15.7 28.725-10.224 9.43 5.472 11.755 18.864 5.37 29.808z" fill="#040000" p-id="4016"></path></svg></a>
                <a href="//'.$_SERVER['HTTP_HOST'].'/GmOauth?site=Huawei" class="btn btn-rounded btn-sm btn-icon btn-default" data-toggle="tooltip" data-placement="bottom" data-original-title="华为账号登录"><svg t="1613135638923" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3874" width="25" height="25"><path d="M446.50124 183.429683s-71.363328-3.247974-116.870777 55.568596c-45.528939 58.814524-11.337209 148.651598 10.248411 194.994066 21.584598 46.341445 135.480624 228.190119 139.664923 232.190223 4.151553 4.010337 9.154497 2.322905 9.42772-1.276063 0.275269-3.569292 13.010315-264.227893 2.76088-328.498686-10.248412-64.272839-40.591487-145.927557-45.231157-152.978136zM199.86091 304.604486c-8.156774 0.87288-66.697051 59.253522-71.973218 116.331495-5.301749 57.07388 19.128663 95.057983 87.341231 140.499941 68.664869 48.689932 231.852532 138.264017 234.849796 130.547265 3.001357-7.696286-63.235207-125.387755-117.44076-210.986295-54.201459-85.596494-124.656091-177.262216-132.777049-176.392406z m21.946848 526.487969c49.38578 22.079878 126.322034-27.508517 147.604756-41.930994 19.792788-15.121396 56.353472-42.919509 56.353473-42.919509l-279.836383 7.489578s26.491351 55.276954 75.878154 77.360925z m7.030114-226.13532c-50.083675-25.069979-153.122423-82.662675-157.154249-81.560574-4.030803 1.100054-19.553335 72.743768 12.670577 126.742613 32.223912 53.998845 94.978165 70.511937 123.777071 74.924433 32.379455 4.613064 222.75841 2.551102 221.482347-1.277086-1.122567-3.341095-150.662395-93.782943-200.775746-118.829386z m464.281185-365.957833c-45.503357-58.81657-116.869754-55.568596-116.869754-55.568596-4.63353 7.048533-34.983769 88.703251-45.231157 152.978137-10.249435 64.271816 2.517333 324.928371 2.789532 328.500732 0.275269 3.595898 5.244444 5.281283 9.423627 1.27504 4.1536-4.002151 118.081349-185.850825 139.667993-232.19227 21.553898-46.342468 55.748698-136.177496 10.219759-194.993043zM951.064874 523.395538c-4.02978-1.103124-107.068528 56.492642-157.151179 81.560574-50.083675 25.045419-199.652156 115.488291-200.748117 118.829385-1.302669 3.827165 189.076286 5.891174 221.455741 1.277087 28.79686-4.412496 91.551112-20.925588 123.777071-74.924434 32.253588-53.997821 16.697287-125.643582 12.666484-126.742612zM653.344169 789.161461c21.278629 14.421454 98.245581 64.010873 147.634432 41.930994 49.384757-22.086018 75.881225-77.360925 75.881224-77.360925l-279.837406-7.489578c-0.002047 0 36.556591 27.802206 56.32175 42.919509z m241.551428-368.226503c-5.304819-57.07695-63.84305-115.457592-71.970148-116.331495-8.151657-0.86981-78.608336 90.795912-132.809795 176.393429-54.207599 85.59854-120.439046 203.293079-117.438713 210.986295 3.000334 7.715729 166.212556-81.857333 234.851842-130.547265 68.208475-45.442982 92.672656-83.426061 87.366814-140.500964z" p-id="3875" fill="#d81e06"></path></svg></a>
                <a href="//'.$_SERVER['HTTP_HOST'].'/GmOauth?site=Gitlab" class="btn btn-rounded btn-sm btn-icon btn-default" data-toggle="tooltip" data-placement="bottom" data-original-title="Gitlab账号登录"><svg t="1613135606600" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3332" width="25" height="25"><path d="M932.317184 567.76704L885.10464 422.46144l-93.57312-287.997952c-4.8128-14.81728-25.776128-14.81728-30.590976 0L667.36128 422.459392H356.62848L263.051264 134.46144c-4.8128-14.81728-25.776128-14.81728-30.593024 0l-93.57312 287.997952-47.210496 145.309696a32.165888 32.165888 0 0 0 11.68384 35.96288l408.6272 296.890368L920.61696 603.734016c11.272192-8.192 15.990784-22.71232 11.68384-35.964928" fill="#FC6D26" p-id="3333"></path><path d="M512.002048 900.62848l155.365376-478.171136H356.634624z" fill="#E24329" p-id="3334"></path><path d="M512.004096 900.62848L356.63872 422.47168H138.901504z" fill="#FC6D26" p-id="3335"></path><path d="M138.891264 422.465536l-47.214592 145.309696a32.16384 32.16384 0 0 0 11.685888 35.96288L511.991808 900.62848z" fill="#FCA326" p-id="3336"></path><path d="M138.893312 422.459392h217.737216L263.053312 134.46144c-4.8128-14.819328-25.778176-14.819328-30.590976 0z" fill="#E24329" p-id="3337"></path><path d="M512.002048 900.62848l155.365376-478.154752H885.10464z" fill="#FC6D26" p-id="3338"></path><path d="M885.11488 422.465536l47.214592 145.309696a32.16384 32.16384 0 0 1-11.685888 35.96288L512.014336 900.62848z" fill="#FCA326" p-id="3339"></path><path d="M885.096448 422.459392H667.36128l93.577216-287.997952c4.814848-14.819328 25.778176-14.819328 30.590976 0z" fill="#E24329" p-id="3340"></path></svg></a>
                <a href="//'.$_SERVER['HTTP_HOST'].'/GmOauth?site=Aliyun" class="btn btn-rounded btn-sm btn-icon btn-default" data-toggle="tooltip" data-placement="bottom" data-original-title="阿里云账号登录"><svg t="1613759792395" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="614" width="25" height="25"><path d="M959.2 383.9c-0.3-82.1-66.9-148.6-149.1-148.6H575.9l21.6 85.2 201 43.7c18.3 4.2 32.1 20.3 32.9 39.7 0.1 0.5 0.1 216.1 0 216.6-0.8 19.4-14.6 35.5-32.9 39.7l-201 43.7-21.6 85.3h234.2c82.1 0 148.8-66.5 149.1-148.6V383.9zM225.5 660.4c-18.3-4.2-32.1-20.3-32.9-39.7-0.1-0.6-0.1-216.1 0-216.6 0.8-19.4 14.6-35.5 32.9-39.7l201-43.7 21.6-85.2H213.8c-82.1 0-148.8 66.4-149.1 148.6V641c0.3 82.1 67 148.6 149.1 148.6H448l-21.6-85.3-200.9-43.9z m200.9-158.8h171v21.3h-171z" p-id="615" fill="#ff6a00"></path></svg></a>
                <a href="//'.$_SERVER['HTTP_HOST'].'/GmOauth?site=Alipay" class="btn btn-rounded btn-sm btn-icon btn-default" data-toggle="tooltip" data-placement="bottom" data-original-title="支付宝账号登录"><svg t="1613808648403" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="7759" width="25" height="25"><path d="M230.4 576.512c-12.288 9.728-25.088 24.064-28.672 41.984-5.12 24.576-1.024 55.296 22.528 79.872 28.672 29.184 72.704 37.376 91.648 38.912 51.2 3.584 105.984-22.016 147.456-50.688 16.384-11.264 44.032-34.304 70.144-69.632-59.392-30.72-133.632-64.512-212.48-61.44-40.448 1.536-69.632 9.728-90.624 20.992z m752.64 135.68c26.112-61.44 40.96-129.024 40.96-200.192C1024 229.888 794.112 0 512 0S0 229.888 0 512s229.888 512 512 512c170.496 0 321.536-83.968 414.72-211.968-88.064-43.52-232.96-115.712-322.56-159.232-42.496 48.64-105.472 97.28-176.64 118.272-44.544 13.312-84.992 18.432-126.976 9.728-41.984-8.704-72.704-28.16-90.624-47.616-9.216-10.24-19.456-22.528-27.136-37.888 0.512 1.024 1.024 2.048 1.024 3.072 0 0-4.608-7.68-7.68-19.456-1.536-6.144-3.072-11.776-3.584-17.92-0.512-4.096-0.512-8.704 0-12.8-0.512-7.68 0-15.872 1.536-24.064 4.096-20.48 12.8-44.032 35.328-65.536 49.152-48.128 114.688-50.688 148.992-50.176 50.176 0.512 138.24 22.528 211.968 48.64 20.48-43.52 33.792-90.112 41.984-121.344h-307.2v-33.28h157.696v-66.56H272.384V302.08h190.464V235.52c0-9.216 2.048-16.384 16.384-16.384h74.752V302.08h207.36v33.28h-207.36v66.56h165.888s-16.896 92.672-68.608 184.32c115.2 40.96 278.016 104.448 331.776 125.952z" fill="#06B4FD" p-id="7760"></path></svg></a>
            </div>';
    }
}
