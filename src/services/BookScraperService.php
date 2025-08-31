<?php

class BookScraperService
{

  /**
   * Scrape book information from a URL and add it to the library
   * 
   * @param string $url The URL to scrape book information from
   * @return array|false Book data array or false on failure
   */
  public function scrapeBookFromUrl($url)
  {
    try {
      debug_log("Scraping book from URL: " . $url);

      // Make HTTP request using cURL
      $html = file_get_contents($url);

      if ($html === false) {
        debug_log("Failed to fetch URL: " . $url);
        return false;
      }

      // Extract book information
      $bookData = $this->extractBookData($html, $url);

      if ($bookData) {
        debug_log("Successfully scraped book data", $bookData);
        return $bookData;
      } else {
        debug_log("Failed to extract book data from URL: " . $url);
        return false;
      }
    } catch (Exception $e) {
      debug_log("Error scraping book from URL: " . $url . " - " . $e->getMessage());
      return false;
    }
  }

  /**
   * Fetch HTML content from a URL
   * 
   * @param string $url The URL to fetch
   * @return string|false The HTML content or false on failure
   */
  private function fetchUrl($url)
  {
    // Use file_get_contents as requested
    $context = stream_context_create([
      'http' => [
        'method' => 'GET',
        'header' => [
          'User-Agent: Lib4All Book Scraper 1.0',
          'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
          'Accept-Language: en-US,en;q=0.5',
          'Accept-Encoding: gzip, deflate',
          'Connection: keep-alive',
        ],
        'timeout' => 30,
      ],
    ]);

    $html = @file_get_contents($url, false, $context);

    if ($html === false) {
      // Fallback to cURL if file_get_contents fails
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Lib4All Book Scraper 1.0');
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $html = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      curl_close($ch);

      if ($httpCode < 200 || $httpCode >= 300) {
        return false;
      }
    }

    return $html;
  }

  /**
   * Extract book data from HTML content - Adapted from the Symfony code
   * 
   * @param string $html The HTML content
   * @param string $url The source URL
   * @return array|false Book data array or false on failure
   */
  private function extractBookData($html, $url)
  {
    $bookData = [
      'title' => '',
      'author' => '',
      'isbn' => '',
      'published_year' => '',
      'genre' => '',
      'cover_image_url' => '',
      'copies_total' => 1,
      'copies_available' => 1
    ];

    try {
      // Check if this is an Open Library URL
      $isOpenLibrary = strpos($url, 'openlibrary.org') !== false;

      if ($isOpenLibrary) {
        // Extract data specifically for Open Library
        $bookData = $this->extractOpenLibraryData($html, $url);
      } else {
        // Extract cover image URL (similar to $crawler->filter('img.w-full')->attr('src'))
        $coverImageUrl = $this->extractByPattern($html, '/<img[^>]*class=[\'"][^\'"]*w-full[^\'"]*[\'"][^>]*src=[\'"]([^\'"]*)[\'"][^>]*>/i', 1);
        if (!$coverImageUrl) {
          // Try other common image selectors
          $coverImageUrl = $this->extractByPattern($html, '/<img[^>]*src=[\'"]([^\'"]*(cover|book)[^\'"]*)[\'"][^>]*>/i', 1);
        }

        if ($coverImageUrl) {
          // Handle relative URLs
          $bookData['cover_image_url'] = $this->resolveUrl($url, $coverImageUrl);
        }

        // Extract title (similar to $crawler->filter('.text-3xl')->first()->text())
        $title = $this->extractByPattern($html, '/<[^>]*class=[\'"][^\'"]*text-3xl[^\'"]*[\'"][^>]*>(.*?)<\/[^>]*>/i', 1);
        if (!$title) {
          // Try h1 tags
          $title = $this->extractByPattern($html, '/<h1[^>]*>(.*?)<\/h1>/i', 1);
        }

        if ($title) {
          $bookData['title'] = trim(strip_tags($title));
        }

        // Extract author (similar to $crawler->filter('.italic')->first()->text())
        $author = $this->extractByPattern($html, '/<[^>]*class=[\'"][^\'"]*italic[^\'"]*[\'"][^>]*>(.*?)<\/[^>]*>/i', 1);
        if (!$author) {
          // Try other common author selectors
          $author = $this->extractByPattern($html, '/by\\s+([A-Z][a-z]+(?:\\s+[A-Z][a-z]+)*)/i', 1);
        }

        if ($author) {
          $bookData['author'] = trim(strip_tags($author));
        }

        // Extract meta information (similar to looking for ISBNS in div.mb-1)
        $metaJson = null;
        $metaDivs = $this->extractAllByPattern($html, '/<div[^>]*class=[\'"][^\'"]*mb-1[^\'"]*[\'"][^>]*>(.*?)<\/div>/i', 1);

        foreach ($metaDivs as $metaDiv) {
          if (strpos($metaDiv, 'isbns') !== false) {
            // Try to extract JSON from the div
            if (preg_match('/(\\{[^}]*isbns[^}]*\\})/', $metaDiv, $jsonMatches)) {
              $metaJson = json_decode($jsonMatches[1], true);
              break;
            }
          }
        }

        // Extract ISBN from meta JSON or fallback patterns
        if ($metaJson && isset($metaJson['isbns'][0])) {
          $bookData['isbn'] = $metaJson['isbns'][0];
        } else {
          // Try to find ISBN in text
          $isbn = $this->extractByPattern($html, '/ISBN[\\s:]*([0-9\\-X]{10,17})/i', 1);
          if ($isbn) {
            $bookData['isbn'] = $isbn;
          } else {
            // Generate a unique ISBN if not found
            $bookData['isbn'] = 'ISBN' . substr(md5($bookData['title'] . $bookData['author']), 0, 10);
          }
        }

        // Extract publisher (similar to $crawler->filter('div.text-md')->first()->text())
        $publisher = $this->extractByPattern($html, '/<[^>]*class=[\'"][^\'"]*text-md[^\'"]*[\'"][^>]*>(.*?)<\/[^>]*>/i', 1);
        if ($publisher) {
          // Store publisher in genre field for now
          $bookData['genre'] = trim(strip_tags($publisher));
        }

        // Extract published year (from meta JSON or text)
        if ($metaJson && isset($metaJson['year'])) {
          $bookData['published_year'] = $metaJson['year'];
        } else {
          // Try to find year in text
          $year = $this->extractByPattern($html, '/\\b(19|20)\\d{2}\\b/', 0);
          if ($year && $year >= 1900 && $year <= date('Y')) {
            $bookData['published_year'] = $year;
          } else {
            $bookData['published_year'] = date('Y');
          }
        }

        // Extract page count (from meta JSON)
        if ($metaJson && isset($metaJson['last_page'])) {
          // Store page count in a custom field (we'll add it to our data)
          $bookData['page_count'] = $metaJson['last_page'];
        }
      }

      // If title is still empty, use a default
      if (empty($bookData['title'])) {
        $bookData['title'] = 'Unknown Title';
      }

      // If author is still empty, use a default
      if (empty($bookData['author'])) {
        $bookData['author'] = 'Unknown Author';
      }

      return $bookData;
    } catch (Exception $e) {
      debug_log("Error extracting book data: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Extract book data specifically from Open Library HTML
   * 
   * @param string $html The HTML content
   * @param string $url The source URL
   * @return array Book data array
   */
  private function extractOpenLibraryData($html, $url)
  {
    $bookData = [
      'title' => '',
      'author' => '',
      'isbn' => '',
      'published_year' => '',
      'genre' => '',
      'cover_image_url' => '',
      'copies_total' => 1,
      'copies_available' => 1
    ];

    // Extract title from h1.work-title
    $title = $this->extractByPattern($html, '/<h1[^>]*class="[^"]*work-title[^"]*"[^>]*>(.*?)<\/h1>/i', 1);
    if (!$title) {
      $title = $this->extractByPattern($html, '/<h1[^>]*class="work-title"[^>]*>(.*?)<\/h1>/i', 1);
    }
    if ($title) {
      $bookData['title'] = trim(strip_tags($title));
    }

    // Extract author from edition-byline
    $author = $this->extractByPattern($html, '/<h2[^>]*class="[^"]*edition-byline[^"]*"[^>]*>(.*?)<\/h2>/i', 1);
    if (!$author) {
      $author = $this->extractByPattern($html, '/<h2[^>]*class="edition-byline"[^>]*>(.*?)<\/h2>/i', 1);
    }
    if ($author) {
      // Extract just the author name from the byline
      if (preg_match('/by\s+(<a[^>]*>)?([^<]+)(<\/a>)?/i', $author, $authorMatches)) {
        $bookData['author'] = trim($authorMatches[2]);
      } else {
        $bookData['author'] = trim(strip_tags($author));
      }
    } else {
      // Fallback to itemprop author
      $author = $this->extractByPattern($html, '/<a[^>]*itemprop="author"[^>]*>(.*?)<\/a>/i', 1);
      if ($author) {
        $bookData['author'] = trim(strip_tags($author));
      }
    }

    // Extract ISBNs
    $isbn13 = $this->extractByPattern($html, '/<dt>ISBN 13<\/dt>\s*<dd[^>]*itemprop="isbn"[^>]*>(.*?)<\/dd>/i', 1);
    $isbn10 = $this->extractByPattern($html, '/<dt>ISBN 10<\/dt>\s*<dd[^>]*itemprop="isbn"[^>]*>(.*?)<\/dd>/i', 1);

    if ($isbn13) {
      $bookData['isbn'] = trim(strip_tags($isbn13));
    } elseif ($isbn10) {
      $bookData['isbn'] = trim(strip_tags($isbn10));
    } else {
      // Fallback to data from affiliate links
      $affiliateData = $this->extractByPattern($html, '/<span[^>]*class="affiliate-links-section"[^>]*data-opts="([^"]*)"[^>]*>/i', 1);
      if ($affiliateData) {
        // Try to extract ISBN from the raw data first
        if (preg_match('/"isbn":"([^"]+)"/', $affiliateData, $isbnMatches)) {
          $bookData['isbn'] = $isbnMatches[1];
        } else {
          // Try decoding and then extracting
          $decodedData = html_entity_decode($affiliateData);
          if (preg_match('/"isbn":\s*"([^"]+)"/', $decodedData, $isbnMatches)) {
            $bookData['isbn'] = $isbnMatches[1];
          }
        }
      }
    }

    // Extract published year
    $publishedYear = $this->extractByPattern($html, '/<div[^>]*Publish Date[^>]*>\s*<span[^>]*itemprop="datePublished"[^>]*>(.*?)<\/span>/i', 1);
    if ($publishedYear) {
      $bookData['published_year'] = trim(strip_tags($publishedYear));
    } else {
      // Try to extract from first published date
      $firstPublished = $this->extractByPattern($html, '/First published in (\d{4})/i', 1);
      if ($firstPublished) {
        $bookData['published_year'] = $firstPublished;
      }
    }

    // Extract publisher
    $publisher = $this->extractByPattern($html, '/<a[^>]*itemprop="publisher"[^>]*>(.*?)<\/a>/i', 1);
    if ($publisher) {
      $bookData['genre'] = trim(strip_tags($publisher));
    }

    // Extract cover image
    $coverImageUrl = $this->extractByPattern($html, '/<meta[^>]*property="og:image"[^>]*content="(https:\/\/covers\.openlibrary\.org\/[^"]+)"[^>]*>/i', 1);
    if ($coverImageUrl) {
      $bookData['cover_image_url'] = $coverImageUrl;
    } else {
      // Try alternative cover image pattern
      $coverImageUrl = $this->extractByPattern($html, '/<img[^>]*src="(\/\/covers\.openlibrary\.org\/[^"]+)"[^>]*class="cover"[^>]*>/i', 1);
      if ($coverImageUrl) {
        $bookData['cover_image_url'] = 'https:' . $coverImageUrl;
      }
    }

    return $bookData;
  }

  /**
   * Extract text using a regex pattern
   * 
   * @param string $text The text to search
   * @param string $pattern The regex pattern
   * @param int $group The capture group to return
   * @return string|false The extracted text or false on failure
   */
  private function extractByPattern($text, $pattern, $group = 0)
  {
    if (preg_match($pattern, $text, $matches)) {
      return isset($matches[$group]) ? $matches[$group] : false;
    }
    return false;
  }

  /**
   * Extract all matches using a regex pattern
   * 
   * @param string $text The text to search
   * @param string $pattern The regex pattern
   * @param int $group The capture group to return
   * @return array Array of extracted texts
   */
  private function extractAllByPattern($text, $pattern, $group = 0)
  {
    $results = [];
    if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        if (isset($match[$group])) {
          $results[] = $match[$group];
        }
      }
    }
    return $results;
  }

  /**
   * Resolve relative URL to absolute URL
   * 
   * @param string $baseUrl The base URL
   * @param string $relativeUrl The relative URL
   * @return string Absolute URL
   */
  private function resolveUrl($baseUrl, $relativeUrl)
  {
    // If already absolute, return as-is
    if (strpos($relativeUrl, 'http') === 0) {
      return $relativeUrl;
    }

    // Parse base URL
    $baseParts = parse_url($baseUrl);
    $scheme = $baseParts['scheme'];
    $host = $baseParts['host'];
    $basePath = isset($baseParts['path']) ? dirname($baseParts['path']) : '';

    // Handle relative URL
    if (strpos($relativeUrl, '//') === 0) {
      // Protocol-relative URL
      return $scheme . ':' . $relativeUrl;
    } elseif (strpos($relativeUrl, '/') === 0) {
      // Root-relative URL
      return $scheme . '://' . $host . $relativeUrl;
    } else {
      // Relative to current path
      return $scheme . '://' . $host . $basePath . '/' . $relativeUrl;
    }
  }

  /**
   * Download and save cover image
   * 
   * @param string $imageUrl The URL of the image to download
   * @return string|false The path to the saved image or false on failure
   */
  public function downloadCoverImage($imageUrl)
  {
    if (empty($imageUrl)) {
      return false;
    }

    try {
      debug_log("Downloading cover image from: " . $imageUrl);

      // Download image using cURL
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $imageUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Lib4All Book Scraper 1.0');
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $imageContent = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      curl_close($ch);

      if ($httpCode < 200 || $httpCode >= 300 || $imageContent === false) {
        debug_log("Failed to download image from: " . $imageUrl);
        return false;
      }

      // Generate filename
      $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
      if (empty($extension)) {
        // Try to determine extension from content type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageContent);
        $mimeToExt = [
          'image/jpeg' => 'jpg',
          'image/png' => 'png',
          'image/gif' => 'gif'
        ];
        $extension = isset($mimeToExt[$mimeType]) ? $mimeToExt[$mimeType] : 'jpg';
      }

      $filename = uniqid() . '.' . $extension;
      $savePath = 'uploads/book_covers/' . $filename;
      $fullPath = __DIR__ . '/../' . $savePath;

      // Create directory if it doesn't exist with proper permissions
      $directory = dirname($fullPath);
      if (!is_dir($directory)) {
        if (!mkdir($directory, 0755, true)) {
          debug_log("Failed to create directory: " . $directory);
          return false;
        }
      }

      // Check if directory is writable
      if (!is_writable($directory)) {
        debug_log("Directory is not writable: " . $directory);
        return false;
      }

      // Save image
      if (file_put_contents($fullPath, $imageContent)) {
        debug_log("Successfully saved cover image to: " . $savePath);
        return $savePath;
      } else {
        debug_log("Failed to save cover image to: " . $savePath);
        return false;
      }
    } catch (Exception $e) {
      debug_log("Error downloading cover image: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Add scraped book to the database
   * 
   * @param array $bookData The book data
   * @param PDO $db The database connection
   * @return int|false The book ID or false on failure
   */
  public function addBookToLibrary($bookData, $db)
  {
    try {
      debug_log("Adding scraped book to library", $bookData);

      // Check if database connection is valid
      if (!$db) {
        debug_log("ERROR: Database connection is null");
        return false;
      }

      // Check if database connection is a PDO instance
      if (!($db instanceof PDO)) {
        debug_log("ERROR: Database connection is not a PDO instance");
        return false;
      }

      // Download cover image if URL is provided
      if (!empty($bookData['cover_image_url'])) {
        debug_log("Attempting to download cover image from: " . $bookData['cover_image_url']);
        $coverImagePath = $this->downloadCoverImage($bookData['cover_image_url']);
        if ($coverImagePath !== false) {
          $bookData['cover_image'] = $coverImagePath;
          debug_log("Cover image downloaded successfully: " . $coverImagePath);
        } else {
          debug_log("Failed to download cover image");
        }
      } else {
        debug_log("No cover image URL provided");
      }

      // Prepare book data for insertion
      $bookInsertData = [
        'title' => $bookData['title'],
        'author' => $bookData['author'],
        'isbn' => $bookData['isbn'],
        'published_year' => $bookData['published_year'],
        'genre' => $bookData['genre'],
        'copies_total' => $bookData['copies_total'],
        'copies_available' => $bookData['copies_available'],
        'cover_image' => $bookData['cover_image'] ?? null
      ];

      debug_log("Book data to insert", $bookInsertData);

      // Use the Book model to create the book
      $bookModel = new Book($db);
      debug_log("Calling Book model createBook method");

      $result = $bookModel->createBook($bookInsertData);

      debug_log("Book model createBook result: " . var_export($result, true));

      if ($result) {
        $bookId = $db->lastInsertId();
        debug_log("Successfully added book to library with ID: " . $bookId);
        return $bookId;
      } else {
        debug_log("Failed to add book to library - createBook returned false");
        // Let's check for any PDO errors
        $errorInfo = $db->errorInfo();
        if ($errorInfo && $errorInfo[0] !== '00000') {
          debug_log("PDO Error: " . $errorInfo[2]);
        }
        return false;
      }
    } catch (Exception $e) {
      debug_log("Exception occurred while adding book to library: " . $e->getMessage());
      debug_log("Exception trace: " . $e->getTraceAsString());
      return false;
    }
  }
}

