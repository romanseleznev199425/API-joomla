<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пакетное создание статей в Joomla из JSON</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: #2c3e50; color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .tabs { display: flex; background: #34495e; }
        .tab { padding: 15px 30px; color: white; cursor: pointer; transition: background 0.3s ease; }
        .tab.active { background: #3498db; }
        .tab:hover { background: #2980b9; }
        .tab-content { display: none; padding: 40px; }
        .tab-content.active { display: block; }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; font-size: 1.1em; }
        input, textarea, select { width: 100%; padding: 12px 15px; border: 2px solid #e8e8e8; border-radius: 8px; font-size: 16px; transition: all 0.3s ease; font-family: inherit; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #3498db; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); }
        textarea { min-height: 120px; resize: vertical; }
        .btn { color: white; border: none; padding: 15px 30px; font-size: 18px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; width: 100%; font-weight: 600; margin-top: 10px; }
        .btn-primary { background: linear-gradient(135deg, #3498db, #2980b9); }
        .btn-success { background: linear-gradient(135deg, #27ae60, #229954); }
        .btn-warning { background: linear-gradient(135deg, #f39c12, #e67e22); }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3); }
        .btn:active { transform: translateY(0); }
        .results { margin-top: 30px; padding: 20px; border-radius: 8px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .required::after { content: " *"; color: #e74c3c; }
        .json-preview { background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px; padding: 20px; margin-top: 20px; max-height: 400px; overflow-y: auto; }
        .json-preview pre { white-space: pre-wrap; word-wrap: break-word; font-family: 'Courier New', monospace; font-size: 14px; }
        .file-input-wrapper { position: relative; overflow: hidden; display: inline-block; width: 100%; }
        .file-input-wrapper input[type=file] { position: absolute; left: 0; top: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer; }
        .file-input-label { display: block; padding: 12px 15px; background: #ecf0f1; border: 2px dashed #bdc3c7; border-radius: 8px; text-align: center; cursor: pointer; transition: all 0.3s ease; }
        .file-input-label:hover { background: #d5dbdb; border-color: #95a5a6; }
        .file-name { margin-top: 5px; font-size: 14px; color: #7f8c8d; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; border-left: 4px solid #3498db; }
        .stat-number { font-size: 2em; font-weight: bold; margin-bottom: 5px; }
        .stat-label { font-size: 0.9em; color: #7f8c8d; }
        .stat-success { border-left-color: #27ae60; }
        .stat-error { border-left-color: #e74c3c; }
        .stat-warning { border-left-color: #f39c12; }
        .article-result { padding: 10px 15px; margin-bottom: 10px; border-radius: 5px; border-left: 4px solid #3498db; }
        .article-result.success { background: #d4edda; border-left-color: #27ae60; }
        .article-result.error { background: #f8d7da; border-left-color: #e74c3c; }
        .article-info { display: flex; justify-content: space-between; align-items: center; }
        .article-title { font-weight: bold; flex-grow: 1; }
        .article-status { padding: 3px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .status-success { background: #27ae60; color: white; }
        .status-error { background: #e74c3c; color: white; }
        .custom-fields { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3498db; }
        .custom-fields h4 { margin-bottom: 15px; color: #2c3e50; }
        .field-note { color: #7f8c8d; font-size: 0.85em; margin-top: 5px; display: block; }
        @media (max-width: 768px) { .form-row, .form-row-3, .stats { grid-template-columns: 1fr; } .tab-content { padding: 20px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Пакетное создание статей</h1>
            <p>Joomla 5 API - до 50 статей за раз</p>
        </div>
        
        <div class="tabs">
            <div class="tab active" data-tab="manual">Одна статья</div>
            <div class="tab" data-tab="json">Пакетное создание из JSON</div>
        </div>
        
        <!-- Вкладка одной статьи -->
        <div class="tab-content active" id="manual-tab">
            <form method="POST" id="articleForm">
                <input type="hidden" name="form_type" value="manual">
                
                <div class="form-group">
                    <label class="required">Joomla URL сайта:</label>
                    <input type="url" name="joomla_url" value="<?= isset($_POST['joomla_url']) ? htmlspecialchars($_POST['joomla_url']) : '' ?>" 
                           placeholder="https://ваш-сайт.ru" required>
                </div>
                
                <div class="form-group">
                    <label class="required">API Token:</label>
                    <input type="text" name="api_token" value="<?= isset($_POST['api_token']) ? htmlspecialchars($_POST['api_token']) : '' ?>" 
                           placeholder="Введите ваш API ключ" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="required">Заголовок статьи:</label>
                        <input type="text" name="title" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>" 
                               placeholder="Введите заголовок" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Alias (URL):</label>
                        <input type="text" name="alias" value="<?= isset($_POST['alias']) ? htmlspecialchars($_POST['alias']) : '' ?>" 
                               placeholder="автоматически-сгенерируется">
                        <span class="field-note">Оставьте пустым для автогенерации</span>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="required">ID категории:</label>
                        <input type="number" name="catid" value="<?= isset($_POST['catid']) ? htmlspecialchars($_POST['catid']) : '2' ?>" 
                               min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Статус:</label>
                        <select name="state">
                            <option value="1" <?= (isset($_POST['state']) ? $_POST['state'] : '1') == '1' ? 'selected' : '' ?>>Опубликовано</option>
                            <option value="0" <?= (isset($_POST['state']) ? $_POST['state'] : '1') == '0' ? 'selected' : '' ?>>Не опубликовано</option>
                            <option value="2" <?= (isset($_POST['state']) ? $_POST['state'] : '1') == '2' ? 'selected' : '' ?>>В архиве</option>
                            <option value="-2" <?= (isset($_POST['state']) ? $_POST['state'] : '1') == '-2' ? 'selected' : '' ?>>В корзине</option>
                        </select>
                    </div>
                </div>

                <div class="custom-fields">
                    <h4>📅 Дополнительные поля</h4>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Дата создания:</label>
                            <input type="datetime-local" name="created" value="<?= isset($_POST['created']) ? htmlspecialchars($_POST['created']) : '' ?>">
                            <span class="field-note">Оставьте пустым для текущей даты</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Начало публикации:</label>
                            <input type="datetime-local" name="publish_up" value="<?= isset($_POST['publish_up']) ? htmlspecialchars($_POST['publish_up']) : '' ?>">
                            <span class="field-note">Оставьте пустым для текущей даты</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Вводный текст:</label>
                    <textarea name="introtext" placeholder="Краткое описание статьи..."><?= isset($_POST['introtext']) ? htmlspecialchars($_POST['introtext']) : '' ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Полный текст:</label>
                    <textarea name="fulltext" placeholder="Полное содержание статьи..."><?= isset($_POST['fulltext']) ? htmlspecialchars($_POST['fulltext']) : '' ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Мета-описание:</label>
                        <textarea name="metadesc" placeholder="Meta description для SEO"><?= isset($_POST['metadesc']) ? htmlspecialchars($_POST['metadesc']) : '' ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Ключевые слова:</label>
                        <textarea name="metakey" placeholder="Ключевые слова через запятую"><?= isset($_POST['metakey']) ? htmlspecialchars($_POST['metakey']) : '' ?></textarea>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">🚀 Создать статью</button>
            </form>
        </div>
        
        <!-- Вкладка пакетного создания из JSON -->
        <div class="tab-content" id="json-tab">
            <form method="POST" enctype="multipart/form-data" id="jsonForm">
                <input type="hidden" name="form_type" value="json">
                
                <div class="form-group">
                    <label class="required">Joomla URL сайта:</label>
                    <input type="url" name="joomla_url" value="<?= isset($_POST['joomla_url']) ? htmlspecialchars($_POST['joomla_url']) : '' ?>" 
                           placeholder="https://ваш-сайт.ru" required>
                </div>
                
                <div class="form-group">
                    <label class="required">API Token:</label>
                    <input type="text" name="api_token" value="<?= isset($_POST['api_token']) ? htmlspecialchars($_POST['api_token']) : '' ?>" 
                           placeholder="Введите ваш API ключ" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="required">ID категории для всех статей:</label>
                        <input type="number" name="batch_catid" value="<?= isset($_POST['batch_catid']) ? htmlspecialchars($_POST['batch_catid']) : '2' ?>" 
                               min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Статус для всех статей:</label>
                        <select name="batch_state">
                            <option value="1" <?= (isset($_POST['batch_state']) ? $_POST['batch_state'] : '1') == '1' ? 'selected' : '' ?>>Опубликовано</option>
                            <option value="0" <?= (isset($_POST['batch_state']) ? $_POST['batch_state'] : '1') == '0' ? 'selected' : '' ?>>Не опубликовано</option>
                            <option value="2" <?= (isset($_POST['batch_state']) ? $_POST['batch_state'] : '1') == '2' ? 'selected' : '' ?>>В архиве</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="required">JSON файл со статьями:</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="json_file" id="json_file" accept=".json" required>
                        <div class="file-input-label">
                            📁 Выберите JSON файл со статьями
                            <div class="file-name" id="file-name">Файл не выбран</div>
                        </div>
                    </div>
                    <span class="field-note">Поддерживается до 50 статей в одном файле. Формат: {"articles": [{...}]} или [{...}]</span>
                </div>
                
                <div class="form-group">
                    <label>Предпросмотр данных:</label>
                    <div class="json-preview">
                        <pre id="json-preview">Выберите JSON файл для предпросмотра...</pre>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-warning">🚀 Создать статьи из JSON (пакетно)</button>
            </form>
        </div>
        
        <?php
        // ОСНОВНЫЕ ФУНКЦИИ
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['form_type']) && $_POST['form_type'] === 'json' && isset($_FILES['json_file'])) {
                $result = processBatchJsonUpload($_POST, $_FILES['json_file']);
            } else {
                $result = createArticle($_POST);
            }
            displayResult($result);
        }
        
        function processBatchJsonUpload($postData, $fileData) {
            // Проверка загрузки файла
            if ($fileData['error'] !== UPLOAD_ERR_OK) {
                return ['error' => 'Ошибка загрузки файла', 'error_code' => 'UPLOAD_ERROR', 'details' => getUploadError($fileData['error'])];
            }
            
            // Проверка типа и размера файла
            $fileType = mime_content_type($fileData['tmp_name']);
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($fileType, ['application/json', 'text/plain']) || $fileData['size'] > $maxSize) {
                return ['error' => 'Неверный формат файла или размер превышает 5MB', 'error_code' => 'INVALID_FILE'];
            }
            
            // Чтение и парсинг JSON
            $jsonContent = file_get_contents($fileData['tmp_name']);
            $data = json_decode($jsonContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => 'Ошибка парсинга JSON', 'error_code' => 'JSON_PARSE_ERROR', 'details' => json_last_error_msg()];
            }
            
            // Определяем структуру данных
            $articles = isset($data['articles']) ? $data['articles'] : (is_array($data) && isset($data[0]['title']) ? $data : null);
            
            if (!$articles) {
                return ['error' => 'Неверная структура JSON', 'error_code' => 'INVALID_STRUCTURE', 
                        'details' => 'Ожидается массив статей в ключе "articles" или корневом массиве'];
            }
            
            // Проверка количества статей
            if (count($articles) > 50) {
                return ['error' => 'Слишком много статей', 'error_code' => 'TOO_MANY_ARTICLES', 
                        'details' => 'Максимальное количество статей: 50. В вашем файле: ' . count($articles)];
            }
            
            if (count($articles) === 0) {
                return ['error' => 'Нет статей для создания', 'error_code' => 'NO_ARTICLES'];
            }
            
            return createBatchArticles($postData, $articles);
        }
        
        function createBatchArticles($postData, $articles) {
            $results = ['success' => [], 'errors' => [], 'total' => count($articles), 'processed' => 0];
            
            foreach ($articles as $index => $articleData) {
                $results['processed']++;
                
                // Подготовка данных статьи
                $preparedData = [
                    'title' => $articleData['title'] ?? '',
                    'alias' => $articleData['alias'] ?? '',
                    'catid' => (int)($articleData['catid'] ?? $postData['batch_catid'] ?? 2),
                    'state' => (int)($articleData['state'] ?? $postData['batch_state'] ?? 1),
                    'introtext' => $articleData['introtext'] ?? '',
                    'fulltext' => $articleData['fulltext'] ?? '',
                    'metadesc' => $articleData['metadesc'] ?? '',
                    'metakey' => $articleData['metakey'] ?? '',
                    'created' => $articleData['created'] ?? '',
                    'publish_up' => $articleData['publish_up'] ?? '',
                    'image_intro' => $articleData['image_intro'] ?? '',
                    'image_intro_alt' => $articleData['image_intro_alt'] ?? '',
                    'joomla_url' => $postData['joomla_url'],
                    'api_token' => $postData['api_token'],
                ];
                
                // Валидация обязательных полей - ТОЛЬКО заголовок
                if (empty($preparedData['title'])) {
                    $results['errors'][] = ['index' => $index, 'title' => 'Без названия', 'error' => 'Заголовок статьи обязателен'];
                    continue;
                }
                
                // Создание статьи
                $result = createArticle($preparedData);
                
                if (isset($result['success'])) {
                    $results['success'][] = [
                        'index' => $index,
                        'title' => $preparedData['title'],
                        'id' => $result['data']['id'],
                        'catid' => $result['data']['catid']
                    ];
                } else {
                    $results['errors'][] = [
                        'index' => $index,
                        'title' => $preparedData['title'],
                        'error' => $result['error'],
                        'error_code' => $result['error_code']
                    ];
                }
                
                // Задержка для избежания перегрузки API
                if ($index < count($articles) - 1) usleep(50000); // 50ms
            }
            
            return ['batch_results' => $results, 'source' => 'json'];
        }
        
        function createArticle($data) {
            // Базовые данные статьи согласно формату Joomla API
            $articleData = [
                'title' => trim($data['title']),
                'catid' => (int)($data['catid'] ?? 2),
                'state' => (int)($data['state'] ?? 1),
                'language' => '*',
                'access' => 1,
                'introtext' => $data['introtext'] ?? '',
                'fulltext' => $data['fulltext'] ?? '',
                'metadesc' => $data['metadesc'] ?? '',
                'metakey' => $data['metakey'] ?? '',
                'images' => [
                    'image_intro' => $data['image_intro'] ?? '',
                    'image_intro_alt' => $data['image_intro_alt'] ?? '',
                    'float_intro' => '',
                    'image_intro_caption' => '',
                    'image_fulltext' => '',
                    'image_fulltext_alt' => '',
                    'float_fulltext' => '',
                    'image_fulltext_caption' => '',
                ],
                'urls' => [
                    'urla' => '',
                    'urlatext' => '',
                    'targeta' => '',
                    'urlb' => '',
                    'urlbtext' => '',
                    'targetb' => '',
                    'urlc' => '',
                    'urlctext' => '',
                    'targetc' => '',
                ],
                'metadata' => [
                    'robots' => '',
                    'author' => '',
                    'rights' => ''
                ],
                'attribs' => [
                    'article_layout' => '',
                    'show_title' => '',
                    'link_titles' => '',
                    'show_tags' => '',
                    'show_intro' => '',
                    'info_block_position' => '',
                    'info_block_show_title' => '',
                    'show_category' => '',
                    'link_category' => '',
                    'show_parent_category' => '',
                    'link_parent_category' => '',
                    'show_author' => '',
                    'link_author' => '',
                    'show_create_date' => '',
                    'show_modify_date' => '',
                    'show_publish_date' => '',
                    'show_item_navigation' => '',
                    'show_hits' => '',
                    'show_noauth' => '',
                    'urls_position' => '',
                    'alternative_readmore' => '',
                    'article_page_title' => '',
                    'show_publishing_options' => '',
                    'show_article_options' => '',
                    'show_urls_images_backend' => '',
                    'show_urls_images_frontend' => '',
                ]
            ];
       
            // Добавляем alias если указан
            if (!empty(trim($data['alias'] ?? ''))) {
                $articleData['alias'] = trim($data['alias']);
            }
            
            // Обработка дат
            if (!empty($data['created'])) {
                $articleData['created'] = formatDateForJoomla($data['created']);
            }
            if (!empty($data['publish_up'])) {
                $articleData['publish_up'] = formatDateForJoomla($data['publish_up']);
            }
            
            // Валидация - ТОЛЬКО заголовок обязателен
            if (empty($articleData['title'])) {
                return ['error' => 'Заголовок статьи обязателен', 'error_code' => 'MISSING_TITLE'];
            }
            
            // Отправка в API
            $apiUrl = rtrim($data['joomla_url'], '/') . '/api/index.php/v1/content/articles';
            $token = $data['api_token'];
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($articleData),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-Joomla-Token: ' . $token,
                    'Accept: application/vnd.api+json'
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                return ['error' => 'Ошибка соединения: ' . $curlError, 'error_code' => 'CURL_ERROR'];
            }
            
            $responseData = json_decode($response, true);
            
            if ($httpCode === 200) {
                return [
                    'success' => true,
                    'data' => [
                        'id' => $responseData['data']['id'],
                        'title' => $responseData['data']['attributes']['title'],
                        'catid' => $responseData['data']['attributes']['catid'],
                        'state' => $responseData['data']['attributes']['state']
                    ]
                ];
            } else {
                $errorMessage = 'Ошибка HTTP: ' . $httpCode;
                if (isset($responseData['errors'][0]['title'])) {
                    $errorMessage = $responseData['errors'][0]['title'];
                }
                
                return [
                    'error' => $errorMessage, 
                    'error_code' => 'HTTP_' . $httpCode,
                    'details' => "Response: " . substr($response, 0, 500)
                ];
            }
        }
        
        function formatDateForJoomla($dateString) {
            try {
                $date = new DateTime($dateString);
                return $date->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                return date('Y-m-d H:i:s');
            }
        }
        
        function displayResult($result) {
            if (isset($result['batch_results'])) {
                displayBatchResults($result['batch_results']);
            } elseif (isset($result['success'])) {
                displaySingleResult($result);
            } else {
                displayError($result);
            }
        }
        
        function displayBatchResults($results) {
            $total = $results['total'];
            $successCount = count($results['success']);
            $errorCount = count($results['errors']);
            $successRate = $total > 0 ? round(($successCount/$total)*100) : 0;
            
            echo '<div class="results ' . ($errorCount === 0 ? 'success' : ($successCount > 0 ? 'warning' : 'error')) . '">';
            echo '<h3>📊 Результаты пакетного создания</h3>';
            
            // Статистика
            echo '<div class="stats">';
            echo '<div class="stat-card"><div class="stat-number">' . $total . '</div><div class="stat-label">Всего статей</div></div>';
            echo '<div class="stat-card stat-success"><div class="stat-number">' . $successCount . '</div><div class="stat-label">Успешно</div></div>';
            echo '<div class="stat-card stat-error"><div class="stat-number">' . $errorCount . '</div><div class="stat-label">Ошибок</div></div>';
            echo '<div class="stat-card stat-warning"><div class="stat-number">' . $successRate . '%</div><div class="stat-label">Успешность</div></div>';
            echo '</div>';
            
            // Детальные результаты
            if ($successCount > 0) {
                echo '<h4>✅ Успешно созданные статьи:</h4>';
                foreach ($results['success'] as $item) {
                    echo '<div class="article-result success">';
                    echo '<div class="article-info">';
                    echo '<span class="article-title">' . htmlspecialchars($item['title']) . '</span>';
                    echo '<span class="article-status status-success">ID: ' . $item['id'] . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            
            if ($errorCount > 0) {
                echo '<h4>❌ Статьи с ошибками:</h4>';
                foreach ($results['errors'] as $item) {
                    echo '<div class="article-result error">';
                    echo '<div class="article-info">';
                    echo '<span class="article-title">' . htmlspecialchars($item['title']) . '</span>';
                    echo '<span class="article-status status-error">Ошибка</span>';
                    echo '</div>';
                    echo '<div style="margin-top: 5px; font-size: 0.9em; color: #721c24;">' . htmlspecialchars($item['error']) . '</div>';
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        
        function displaySingleResult($result) {
            echo '<div class="results success">';
            echo '<h3>✅ Статья успешно создана!</h3>';
            echo '<p><strong>ID:</strong> ' . $result['data']['id'] . '</p>';
            echo '<p><strong>Заголовок:</strong> ' . $result['data']['title'] . '</p>';
            echo '<p><strong>Категория:</strong> ' . $result['data']['catid'] . '</p>';
            echo '<p><strong>Статус:</strong> ' . getStatusText($result['data']['state']) . '</p>';
            echo '</div>';
        }
        
        function displayError($result) {
            echo '<div class="results error">';
            echo '<h3>❌ Ошибка при создании статьи</h3>';
            echo '<p><strong>Код ошибки:</strong> ' . $result['error_code'] . '</p>';
            echo '<p><strong>Сообщение:</strong> ' . $result['error'] . '</p>';
            if (isset($result['details'])) {
                echo '<p><strong>Детали:</strong> ' . htmlspecialchars($result['details']) . '</p>';
            }
            echo '</div>';
        }
        
        function getUploadError($errorCode) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'Файл слишком большой',
                UPLOAD_ERR_FORM_SIZE => 'Файл слишком большой',
                UPLOAD_ERR_PARTIAL => 'Файл загружен частично',
                UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
                UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка',
                UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
                UPLOAD_ERR_EXTENSION => 'Расширение PHP остановило загрузку'
            ];
            return $errors[$errorCode] ?? 'Неизвестная ошибка';
        }
        
        function getStatusText($state) {
            $statuses = ['1' => 'Опубликовано', '0' => 'Не опубликовано', '2' => 'В архиве', '-2' => 'В корзине'];
            return $statuses[$state] ?? 'Неизвестно';
        }
        ?>
    </div>

    <script>
        // Переключение вкладок
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(this.getAttribute('data-tab') + '-tab').classList.add('active');
            });
        });
        
        // Автогенерация alias из заголовка
        document.querySelector('input[name="title"]')?.addEventListener('blur', function() {
            const aliasField = document.querySelector('input[name="alias"]');
            if (aliasField && !aliasField.value.trim()) {
                aliasField.value = generateAlias(this.value);
            }
        });
        
        // Обработка выбора JSON файла
        document.getElementById('json_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileName = document.getElementById('file-name');
            const preview = document.getElementById('json-preview');
            
            if (file) {
                fileName.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const json = JSON.parse(e.target.result);
                        const articles = json.articles || (Array.isArray(json) ? json : []);
                        const articleCount = articles.length;
                        
                        let previewText = `📊 Найдено статей: ${articleCount}\n`;
                        previewText += `📅 Обязательные поля: title\n`;
                        previewText += `📝 Опциональные поля: alias, introtext, fulltext, catid, state, image_intro, image_intro_alt, metadesc, metakey, created, publish_up\n\n`;
                        previewText += JSON.stringify(json, null, 2);
                        preview.textContent = previewText;
                    } catch (error) {
                        preview.textContent = '❌ Ошибка чтения JSON: ' + error.message;
                    }
                };
                reader.readAsText(file);
            } else {
                fileName.textContent = 'Файл не выбран';
                preview.textContent = 'Выберите JSON файл для предпросмотра...';
            }
        });
        
        function generateAlias(title) {
            return title.toLowerCase()
                .replace(/[^\w\u0400-\u04FF]+/g, '-')
                .replace(/^-+|-+$/g, '')
                .replace(/ь|ъ/g, '')
                .replace(/а/g, 'a').replace(/б/g, 'b').replace(/в/g, 'v').replace(/г/g, 'g')
                .replace(/д/g, 'd').replace(/е/g, 'e').replace(/ё/g, 'yo').replace(/ж/g, 'zh')
                .replace(/з/g, 'z').replace(/и/g, 'i').replace(/й/g, 'y').replace(/к/g, 'k')
                .replace(/л/g, 'l').replace(/м/g, 'm').replace(/н/g, 'n').replace(/о/g, 'o')
                .replace(/п/g, 'p').replace(/р/g, 'r').replace(/с/g, 's').replace(/т/g, 't')
                .replace(/у/g, 'u').replace(/ф/g, 'f').replace(/х/g, 'h').replace(/ц/g, 'ts')
                .replace(/ч/g, 'ch').replace(/ш/g, 'sh').replace(/щ/g, 'sch').replace(/ы/g, 'yi')
                .replace(/э/g, 'e').replace(/ю/g, 'yu').replace(/я/g, 'ya');
        }
    </script>
</body>
</html>