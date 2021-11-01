import request from '@/utils/request';
import Resource from '@/api/resource';

class UserResource extends Resource {
  constructor() {
    super('users');
  }

  permissions(id) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'get',
    });
  }

  updatePermission(id, permissions) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'put',
      data: permissions,
    });
  }

  checkBalance(id){
    return request({
      url: '/' + this.uri + '/' + id + '/checkbalance',
      method: 'get',
    });
  }

  rectifyBalance(id){
    return request({
      url: '/' + this.uri + '/' + id + '/rectifybalance',
      method: 'get',
    });
  }

  grantToken(id, tokenAmount){
    return request({
      url: '/' + this.uri + '/' + id + '/granttokens',
      method: 'post',
      data: {
        token_amount: tokenAmount,
      },
    });
  }
}

export { UserResource as default };
