<?php

namespace App\Controllers\Backend;

use App\Core\Request;
use App\Models\VideoLink;
use App\Models\Platform;
use App\Models\Content;
use App\Constants\LinkStatus;
use App\Constants\ContentType;

class VideoLinkController extends BackendController
{
    private Platform $platformModel;
    private Content $contentModel;

    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->curModel = new VideoLink();
        $this->platformModel = new Platform();
        $this->contentModel = new Content();
    }

    public function index(Request $request): void
    {
        // 获取搜索过滤条件
        $filters = $this->getSearchFilters(['id', 'content_id', 'platform_id', 'external_video_id', 'status_id', 'order_by'], $request);

        // 根据过滤条件获取所有符合条件的视频链接数据（不分页，由JS处理分页）
        $videoLinks = VideoLink::findAllWithFilters($filters);

        // 获取每个video_link的关联信息
        foreach ($videoLinks as &$link) {
            $contentInfo = $this->curModel->getRelatedContent($link['id']);
            $platformInfo = $this->curModel->getRelatedPlatform($link['id']);
            $link['content_title'] = $contentInfo ? ($contentInfo['title_cn'] ?: $contentInfo['title_en']) : '';
            $link['platform_name'] = $platformInfo ? $platformInfo['name'] : '';
            $link['platform_code'] = $platformInfo ? $platformInfo['code'] : '';
        }

        $stats = $this->curModel->getStats();

        // 获取下拉选择所需数据
        $platformsList = Platform::loadList([], ['id'=>'id', 'text'=>'name']);
        $contentsList = Content::loadList([
            'content_type_id' => ContentType::VIDEO->value
        ], ['id'=>'id', 'text'=>'title_cn']);

        $this->render('video_links/index', [
            'videoLinks' => $videoLinks,
            'filters' => $filters,
            'stats' => $stats,
            'platformsList' => $platformsList,
            'contentsList' => $contentsList,
            'pageTitle' => '视频链接管理 - 视频分享网站管理后台',
            'css_files' => ['content_list_2.css'],
            'js_files' => ['video_link_list_1.js']
        ]);
    }

    public function edit(Request $request): void
    {
        $id = (int)$request->getParam(0);

        // 1. 通过ID查找VideoLink实例
        $videoLink = VideoLink::find($id);
        if (!$videoLink) {
            $this->redirect('/video-links');
            return;
        }

        // 处理 POST 请求（表单提交）
        if ($request->isPost()) {
            $postId = (int)($request->post('id') ?? 0);

            if (!$postId || $postId !== $id) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid video link ID']);
                return;
            }

            // 4. 对 POST 的数值进行提取并填充回 $videoLink
            $data = [
                'content_id' => (int)($request->post('content_id') ?? 0),
                'platform_id' => (int)($request->post('platform_id') ?? 0),
                'external_url' => $request->post('external_url'),
                'external_video_id' => $request->post('external_video_id'),
                'play_cnt' => (int)($request->post('play_cnt') ?? 0),
                'like_cnt' => (int)($request->post('like_cnt') ?? 0),
                'favorite_cnt' => (int)($request->post('favorite_cnt') ?? 0),
                'download_cnt' => (int)($request->post('download_cnt') ?? 0),
                'comment_cnt' => (int)($request->post('comment_cnt') ?? 0),
                'share_cnt' => (int)($request->post('share_cnt') ?? 0),
                'status_id' => (int)($request->post('status_id') ?? LinkStatus::VALID->value)
            ];

            $videoLink->fill($data);

            // 5. 使用 VideoLink 的 validate 对提取的 post 数值进行验证
            if (!$videoLink->validate()) {
                // 6. 如果验证失败，使用 $videoLink->errors 返回给 view
                $this->renderEditForm($videoLink);
                return;
            }

            try {
                // 7. 验证通过，写入数据库
                if ($videoLink->save()) {
                    // 成功后跳转到列表页面
                    $this->setFlashMessage('视频链接编辑成功', 'success');
                    $this->redirect('/video-links');
                } else {
                    // 保存失败，返回编辑页面并显示错误
                    $this->renderEditForm($videoLink);
                }
            } catch (\Exception $e) {
                error_log("VideoLink update error: " . $e->getMessage());
                $videoLink->errors['general'] = '更新失败: ' . $e->getMessage();
                $this->renderEditForm($videoLink);
            }
            return;
        }

        // 2. 把 $videoLink 传递到 view 实现渲染（GET请求 - 显示表单）
        $this->renderEditForm($videoLink);
    }

    private function renderEditForm(VideoLink $videoLink): void
    {
        $platformsList = Platform::loadList();
        $contentsList = Content::loadList([
            'content_type_id' => ContentType::VIDEO->value
        ]);

        $this->render('video_links/edit', [
            'videoLink' => $videoLink,
            'platformsList' => $platformsList,
            'contentsList' => $contentsList,
            'pageTitle' => '编辑视频链接 - 视频分享网站管理后台',
            'css_files' => ['content_edit_10.css'],
            'js_files' => ['form_utils_2.js', 'video_link_edit_1.js']
        ]);
    }

    public function create(Request $request): void
    {
        // 1. 创建新的VideoLink实例
        $videoLink = new VideoLink();

        // 检查是否有content_id参数（从content列表页面快速创建）
        $contentId = (int)$request->get('content_id', 0);
        if ($contentId > 0) {
            $videoLink->content_id = $contentId;
        }

        // 处理 POST 请求（表单提交）
        if ($request->isPost()) {
            // 4. 对 POST 的数值进行提取并填充回 $videoLink
            $data = [
                'content_id' => (int)($request->post('content_id') ?? 0),
                'platform_id' => (int)($request->post('platform_id') ?? 0),
                'external_url' => $request->post('external_url'),
                'external_video_id' => $request->post('external_video_id'),
                'play_cnt' => (int)($request->post('play_cnt') ?? 0),
                'like_cnt' => (int)($request->post('like_cnt') ?? 0),
                'favorite_cnt' => (int)($request->post('favorite_cnt') ?? 0),
                'download_cnt' => (int)($request->post('download_cnt') ?? 0),
                'comment_cnt' => (int)($request->post('comment_cnt') ?? 0),
                'share_cnt' => (int)($request->post('share_cnt') ?? 0),
                'status_id' => (int)($request->post('status_id') ?? LinkStatus::VALID->value)
            ];

            $videoLink->fill($data);

            // 5. 使用 VideoLink 的 validate 对提取的 post 数值进行验证
            if (!$videoLink->validate()) {
                // 6. 如果验证失败，使用 $videoLink->errors 返回给 view
                $this->renderCreateForm($videoLink);
                return;
            }

            try {
                // 7. 验证通过，写入数据库
                if ($videoLink->save()) {
                    // 成功后跳转到列表页面
                    $this->setFlashMessage('视频链接创建成功', 'success');
                    $this->redirect('/video-links');
                } else {
                    // 保存失败，返回创建页面并显示错误
                    $this->renderCreateForm($videoLink);
                }
            } catch (\Exception $e) {
                error_log("VideoLink creation error: " . $e->getMessage());
                $videoLink->errors['general'] = '创建失败: ' . $e->getMessage();
                $this->renderCreateForm($videoLink);
            }
            return;
        }

        // 2. 把 $videoLink 传递到 view 实现渲染（GET请求 - 显示表单）
        $this->renderCreateForm($videoLink);
    }

    private function renderCreateForm(VideoLink $videoLink): void
    {
        $platformsList = Platform::loadList([], ['id'=>'id', 'text'=>'name']);
        $contentsList = Content::loadList([
            'content_type_id' => ContentType::VIDEO->value
        ], ['id'=>'id', 'text'=>'title_cn']);

        $this->render('video_links/create', [
            'videoLink' => $videoLink,
            'platformsList' => $platformsList,
            'contentsList' => $contentsList,
            'pageTitle' => '创建视频链接 - 视频分享网站管理后台',
            'css_files' => ['content_edit_10.css'],
            'js_files' => ['form_utils_2.js', 'video_link_edit_1.js']
        ]);
    }

    public function destroy(Request $request): void
    {
        $id = (int)$request->getParam(0);

        if (!$id) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid video link ID']);
            return;
        }

        try {
            $this->curModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => '视频链接删除成功']);
        } catch (\Exception $e) {
            error_log("VideoLink deletion error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '删除失败: ' . $e->getMessage()]);
        }
    }

    /**
     * 获取CSV文件必需的字段 - 重写父类方法适配VideoLink模型
     *
     * @return array
     */
    protected function getRequiredCSVFields(): array
    {
        return ['content_id', 'platform_id', 'external_url', 'external_video_id'];
    }
}
