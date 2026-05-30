/**
 * カート関連のJavaScript機能
 */

class CartManager {
    constructor() {
        this.init();
    }

    init() {
        this.updateCartCount();
        this.bindEvents();
    }

    /**
     * イベントバインディング
     */
    bindEvents() {
        // カートに追加ボタン
        document.addEventListener('click', (e) => {
            if (e.target.matches('.add-to-cart-btn')) {
                e.preventDefault();
                this.addToCart(e.target);
            }
        });

        // 数量変更
        document.addEventListener('change', (e) => {
            if (e.target.matches('.cart-quantity-input')) {
                this.updateQuantity(e.target);
            }
        });

        // カートから削除
        document.addEventListener('click', (e) => {
            if (e.target.matches('.remove-from-cart-btn')) {
                e.preventDefault();
                this.removeFromCart(e.target);
            }
        });

        // カートクリア
        document.addEventListener('click', (e) => {
            if (e.target.matches('.clear-cart-btn')) {
                e.preventDefault();
                this.clearCart();
            }
        });
    }

    /**
     * カートに商品を追加
     */
    async addToCart(button) {
        const productId = button.dataset.productId;
        const quantityInput = button.closest('form')?.querySelector('select[name="quantity"]');
        const quantity = quantityInput ? quantityInput.value : 1;

        try {
            button.disabled = true;
            button.textContent = '追加中...';

            const response = await fetch(`/api/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ quantity: parseInt(quantity) })
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage(data.message, 'success');
                this.updateCartCount();
                
                // カートページにいる場合は再読み込み
                if (window.location.pathname === '/cart') {
                    window.location.reload();
                }
            } else {
                this.showMessage(data.message, 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showMessage('カートへの追加に失敗しました。', 'error');
        } finally {
            button.disabled = false;
            button.textContent = 'カートに追加';
        }
    }

    /**
     * カートアイテムの数量を更新
     */
    async updateQuantity(input) {
        const cartItemId = input.dataset.cartItemId;
        const quantity = parseInt(input.value);

        if (quantity < 1) {
            input.value = 1;
            return;
        }

        try {
            const response = await fetch(`/api/cart/update/${cartItemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ quantity })
            });

            const data = await response.json();

            if (data.success) {
                this.updateCartCount();
                this.updateCartTotals();
            } else {
                this.showMessage(data.message, 'error');
                // 元の値に戻す
                input.value = input.dataset.originalValue || 1;
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
            this.showMessage('数量の更新に失敗しました。', 'error');
            input.value = input.dataset.originalValue || 1;
        }
    }

    /**
     * カートから商品を削除
     */
    async removeFromCart(button) {
        const cartItemId = button.dataset.cartItemId;
        
        if (!confirm('この商品をカートから削除しますか？')) {
            return;
        }

        try {
            button.disabled = true;

            const response = await fetch(`/api/cart/remove/${cartItemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage(data.message, 'success');
                this.updateCartCount();
                
                // カートアイテムの行を削除
                const cartRow = button.closest('.cart-item-row');
                if (cartRow) {
                    cartRow.remove();
                }
                
                this.updateCartTotals();
            } else {
                this.showMessage(data.message, 'error');
            }
        } catch (error) {
            console.error('Error removing from cart:', error);
            this.showMessage('商品の削除に失敗しました。', 'error');
        } finally {
            button.disabled = false;
        }
    }

    /**
     * カートを空にする
     */
    async clearCart() {
        if (!confirm('カートを空にしますか？この操作は取り消せません。')) {
            return;
        }

        try {
            const response = await fetch('/api/cart/clear', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage(data.message, 'success');
                this.updateCartCount();
                
                // カートページの場合は再読み込み
                if (window.location.pathname === '/cart') {
                    window.location.reload();
                }
            } else {
                this.showMessage(data.message, 'error');
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
            this.showMessage('カートのクリアに失敗しました。', 'error');
        }
    }

    /**
     * カート内商品数を更新
     */
    async updateCartCount() {
        try {
            const response = await fetch('/api/cart/count', {
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(element => {
                    element.textContent = data.data.count;
                    
                    // カート数が0の場合は非表示
                    if (data.data.count === 0) {
                        element.style.display = 'none';
                    } else {
                        element.style.display = 'inline';
                    }
                });
            }
        } catch (error) {
            console.error('Error updating cart count:', error);
        }
    }

    /**
     * カートの合計金額を更新（カートページ用）
     */
    updateCartTotals() {
        const cartItems = document.querySelectorAll('.cart-item-row');
        let totalAmount = 0;

        cartItems.forEach(item => {
            const priceElement = item.querySelector('.item-price');
            const quantityElement = item.querySelector('.cart-quantity-input');
            
            if (priceElement && quantityElement) {
                const price = parseFloat(priceElement.dataset.price || 0);
                const quantity = parseInt(quantityElement.value || 0);
                totalAmount += price * quantity;
            }
        });

        // 合計金額を更新
        const totalElements = document.querySelectorAll('.cart-total-amount');
        totalElements.forEach(element => {
            element.textContent = '¥' + totalAmount.toLocaleString();
        });
    }

    /**
     * メッセージを表示
     */
    showMessage(message, type = 'info') {
        // 既存のメッセージを削除
        const existingMessage = document.querySelector('.cart-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // メッセージ要素を作成
        const messageDiv = document.createElement('div');
        messageDiv.className = `cart-message alert alert-${type === 'success' ? 'green' : 'red'}-500 bg-${type === 'success' ? 'green' : 'red'}-50 border border-${type === 'success' ? 'green' : 'red'}-200 text-${type === 'success' ? 'green' : 'red'}-700 px-4 py-3 rounded mb-4`;
        messageDiv.textContent = message;

        // ページ上部に挿入
        const container = document.querySelector('main') || document.body;
        container.insertBefore(messageDiv, container.firstChild);

        // 5秒後に自動削除
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }
}

// DOMが読み込まれたらCartManagerを初期化
document.addEventListener('DOMContentLoaded', () => {
    new CartManager();
}); 