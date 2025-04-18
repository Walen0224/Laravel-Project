document.addEventListener('DOMContentLoaded', function () {
    let currentIndex = 0;
    const images = document.querySelectorAll('img[data-index]');
    const thumbnails = document.querySelectorAll('.thumbnail');
    const totalImages = images.length;

    function updateDisplay(newIndex) {
        images[currentIndex].classList.add('hidden');
        thumbnails[currentIndex].classList.remove('border-blue-500');
        thumbnails[currentIndex].classList.add('border-gray-300');

        currentIndex = (newIndex + totalImages) % totalImages;

        images[currentIndex].classList.remove('hidden');
        thumbnails[currentIndex].classList.remove('border-gray-300');
        thumbnails[currentIndex].classList.add('border-blue-500');
    }

    function changeImage(direction) {
        let newIndex = (currentIndex + direction + totalImages) % totalImages;
        updateDisplay(newIndex);
    }

    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', () => updateDisplay(index));
    });

    document.getElementById('leftArrow')?.addEventListener('click', () => changeImage(-1));
    document.getElementById('rightArrow')?.addEventListener('click', () => changeImage(1));

    updateDisplay(0);
    
    // 圖片模態視窗功能
    const imageContainer = document.querySelector('.relative.mb-4');
    if (imageContainer) {
        imageContainer.addEventListener('click', function(event) {
            // 找到目前可見的圖片
            const visibleImage = imageContainer.querySelector('img:not(.hidden)');

            if (visibleImage && event.target.tagName === 'IMG') {
                // 建立覆蓋層
                const overlay = document.createElement('div');
                overlay.classList.add('image-modal-overlay');

                // 建立模態視窗的圖片元素
                const modalImage = document.createElement('img');
                modalImage.src = visibleImage.src;
                modalImage.alt = '放大圖片';
                modalImage.classList.add('image-modal-content');

                // 建立關閉按鈕
                const closeButton = document.createElement('span');
                closeButton.innerHTML = '&times;';
                closeButton.classList.add('image-modal-close');

                // 附加元素
                overlay.appendChild(modalImage);
                overlay.appendChild(closeButton);
                document.body.appendChild(overlay);

                // 防止背景滾動
                document.body.style.overflow = 'hidden';

                // 點擊覆蓋層或關閉按鈕時關閉模態視窗
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay || e.target === closeButton) {
                        document.body.removeChild(overlay);
                        document.body.style.overflow = ''; // 恢復背景滾動
                    }
                });
            }
        });
    }
});

window.addEventListener('load', function () {
    console.log('頁面已完全加載');
    var reportButton = document.getElementById('reportButton');
    if (reportButton) {
        console.log('找到檢舉按鈕');
        reportButton.addEventListener('click', function (e) {
            handleReport(e, '商品', this.dataset.productId);
        });
    } else {
        console.error('未找到檢舉按鈕');
    }
});

function handleReport(event, entityType, entityId) {
    event.preventDefault();
    console.log(`處理檢舉: ${entityType}, ID: ${entityId}`);

    const reportLink = event.target.closest('[data-reports]');
    if (!reportLink) {
        console.error('未找到 data-reports 的元素');
        return;
    }

    const reports = JSON.parse(reportLink.dataset.reports || '{}');
    const storeUrl = reportLink.dataset.storeUrl;
    console.log('檢舉資料:', { reports, storeUrl });

    Swal.fire({
        title: `檢舉${entityType}`,
        html: `
            <select id="reportReason" class="swal2-input">
                <option value="" disabled selected>選擇檢舉原因</option>
                ${Object.entries(reports).map(([key, value]) =>
                    `<option value="${key}">${value.zh_TW || value}</option>`
                ).join('')}
            </select>
            <textarea id="customReason" class="swal2-textarea" placeholder="輸入自定義原因"
                      style="width:80%;height:80px;margin-top:1rem;"></textarea>
        `,
        preConfirm: () => {
            const reportId = document.getElementById('reportReason').value;
            const customReason = document.getElementById('customReason').value;
            if (!reportId && !customReason) {
                Swal.showValidationMessage('請選擇原因或輸入自定義內容');
            }
            return { reportId, customReason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { reportId, customReason } = result.value;
            console.log('送出檢舉:', { reportId, customReason, storeUrl });

            fetch(storeUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${localStorage.getItem("token")}`, // 加入 Token
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    report_type_id: reportId,
                    description: customReason || '無補充說明'
                })
            })
            .then(res => {
                console.log('Response Status:', res.status);
                if (res.status === 401) {
                    throw new Error('未登入');
                }
                return res.json();
            })
            .then(data => {
                Swal.fire('成功', '檢舉已提交', 'success');
            })
            .catch(err => {
                Swal.fire('錯誤', '請稍後再試', 'error');
                console.error("檢舉錯誤:", err);
            });
        }
    });
}

document.getElementById('reportButton')?.addEventListener('click', function(e) {
    handleReport(e, '商品', this.dataset.productId);
});

document.body.addEventListener('click', function(e) {
    console.log('點擊元素:', e.target);
    const trigger = e.target.closest('[data-report-type="message"]');
    if (trigger) {
        console.log('偵測到留言檢舉點擊:', trigger);
        e.preventDefault();
        const messageId = trigger.dataset.messageId;
        handleReport(e, '留言', messageId);
    }
});

// 留言回覆表單切換功能
function toggleReplyForm(messageId) {
    const form = document.getElementById(`replyForm${messageId}`);
    form.classList.toggle('hidden');
    const textarea = form.querySelector('textarea');
    if (!form.classList.contains('hidden')) {
        textarea.focus();
    }
}
